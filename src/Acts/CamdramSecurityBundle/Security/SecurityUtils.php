<?php
namespace Acts\CamdramSecurityBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Acts\CamdramSecurityBundle\Security\Service\ServiceInterface,
    Acts\CamdramSecurityBundle\Security\Acl\Dbal\AclListProvider;

class SecurityUtils
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var ServiceMap
     */
    private $serviceMap;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, $firewall_name = null)
    {
        $this->container = $container;
        if (!$firewall_name) $firewall_name = $container->getParameter('camdram.security.default_firewall');
       // $this->serviceMap  = $this->container->get('camdram.security.service_map.'.$firewall_name);
        $this->serviceMap = array();
    }

    /**
     * @return array
     */
    public function getServices()
    {
        //$services = $this->serviceMap->getServices();

        return array(); //array_keys($services);
    }

    /**
     * @param string  $name
     *
     * @return string
     */
    public function getAuthorizationUrl($name)
    {
        $hasUser = false; //$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED');

        $service = $this->getService($name);
        $checkPath = $this->serviceMap->getServiceCheckPath($name);

        $redirect_uri =  $hasUser
            ? $this->generateUrl('camdram_security_login', array('service' => $name), true)
            : $this->generateUri($checkPath);
        return $service->getAuthorizationUrl($redirect_uri);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getLoginUrl($name)
    {
        // Just to check that this resource owner exists
        $this->getResourceOwner($name);

        return $this->generateUrl('hwi_oauth_service_redirect', array('service' => $name));
    }

    /**
     * Sign the request parameters
     *
     * @param string $method       Request method
     * @param string $url          Request url
     * @param array  $parameters   Parameters for the request
     * @param string $clientSecret Client secret to use as key part of signing
     * @param string $tokenSecret  Optional token secret to use with signing
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function signRequest($method, $url, $parameters, $clientSecret, $tokenSecret = '')
    {
        // Validate required parameters
        foreach (array('oauth_consumer_key', 'oauth_timestamp', 'oauth_nonce', 'oauth_version', 'oauth_signature_method') as $parameter) {
            if (!isset($parameters[$parameter])) {
                throw new \RuntimeException(sprintf('Parameter "%s" must be set.', $parameter));
            }
        }

        // Remove oauth_signature if present
        // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
        if (isset($parameters['oauth_signature'])) {
            unset($parameters['oauth_signature']);
        }

        // Parameters are sorted by name, using lexicographical byte value ordering.
        // Ref: Spec: 9.1.1 (1)
        uksort($parameters, 'strcmp');

        // http_build_query should use RFC3986
        $parts = array(
            $method,
            rawurlencode($url),
            rawurlencode(str_replace(array('%7E','+'), array('~','%20'), http_build_query($parameters))),
        );

        $baseString = implode('&', $parts);

        $keyParts = array(
            rawurlencode($clientSecret),
            rawurlencode($tokenSecret),
        );

        $key = implode('&', $keyParts);

        return base64_encode(hash_hmac('sha1', $baseString, $key, true));
    }

    /**
     * @param string $name
     *
     * @return ResourceOwnerInterface
     *
     * @throws \RuntimeException
     */
    private function getService($name)
    {
        $service = $this->serviceMap->getServiceByName($name);
        if (!$service instanceof ServiceInterface) {
            throw new \RuntimeException(sprintf("No service with name '%s'.", $name));
        }

        return $service;
    }

    /**
     * Get the uri for a given path.
     *
     * @param string $path Path or route
     *
     * @return string
     */
    private function generateUri($path)
    {
        if (0 === strpos($path, 'http') || !$path) {
            return $path;
        }

        if ($path && '/' === $path[0]) {
            return $this->container->get('request')->getUriForPath($path);
        }

        return $this->generateUrl($path, array(), true);
    }

    /**
     * @param string  $route
     * @param array   $params
     * @param boolean $absolute
     *
     * @return string
     */
    private function generateUrl($route, array $params = array(), $absolute = false)
    {
        return $this->container->get('router')->generate($route, $params, $absolute);
    }

    public function getAclEntries($role, $class_name)
    {
        /** @var $aclProvider AclListProvider */
        $aclProvider = $this->container->get('camdram.security.acl.provider');

        if ($role instanceof \Acts\CamdramSecurityBundle\Entity\Group) {
            return $aclProvider->getEntitiesByGroup($role, $class_name);
        }
        if ($role instanceof \Acts\CamdramBundle\Entity\User) {
            return $aclProvider->getEntitiesByUser($role, $class_name);
        }

        return array();
    }

    public function isGranted($attributes, $object, $fully_authenticated = true)
    {
        return $this->container->get('camdram.security.acl.helper')->isGranted($attributes, $object, $fully_authenticated);
    }

    public function hasRole($role)
    {
        return $this->container->get('security.context')->isGranted($role);
    }

    public function ensureRole($role)
    {
        if (false === $this->hasRole($role)) {
            throw new AccessDeniedException();
        }
    }
}