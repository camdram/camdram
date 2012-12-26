<?php


namespace Acts\CamdramSecurityBundle\Security;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Http\HttpUtils;

/**
 * ResourceOwnerMap. Holds several services for a firewall. Lazy
 * loads the appropriate resource owner when requested.
 *
 */
class ServiceMap
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var HttpUtils
     */
    protected $httpUtils;

    /**
     * @var array
     */
    protected $services;

    protected $possibleServices;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container              Container used to lazy load the resource owners.
     * @param HttpUtils          $httpUtils              HttpUtils
     * @param array              $possibleServices Array with possible resource owners names.
     * @param array              $services         Array with configured resource owners.
     */
    public function __construct(ContainerInterface $container, HttpUtils $httpUtils, array $possibleServices, $services)
    {
        $this->container              = $container;
        $this->httpUtils              = $httpUtils;
        $this->possibleServices = $possibleServices;
        $this->services         = $services;
    }

    /**
     * Gets the appropriate resource owner given the name.
     *
     * @param string $name
     *
     * @return null|\Acts\CamdramSecurityBundle\Security\ServiceInterface
     */
    public function getServiceByName($name)
    {
        if (!isset($this->services[$name])) {
            return null;
        }
        if (!in_array($name, $this->possibleServices)) {
            return null;
        }

        $service = $this->container->get('camdram.security.service.'.$name);

        return $service;
    }

    /**
     * Gets the appropriate resource owner for a request.
     *
     * @param Request $request
     *
     * @return null|array
     */
    public function getServiceByRequest(Request $request)
    {
        foreach ($this->services as $name => $options) {
            if ($this->httpUtils->checkRequestPath($request, $options['login_url'])) {
                return array($this->getServiceByName($name), $options['login_url']);
            }
        }

        return null;
    }

    /**
     * Gets the check path for given resource name.
     *
     * @param string $name
     *
     * @return null|string
     */
    public function getServiceCheckPath($name)
    {
        if (isset($this->services[$name])) {
            return $this->services[$name]['login_url'];
        }

        return null;
    }

    /**
     * Get all the resource owners.
     *
     * @return array
     */
    public function getServices()
    {
        return $this->services;
    }
}