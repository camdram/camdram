<?php


namespace Acts\ExternalLoginBundle\Security\Service;

use Symfony\Component\DependencyInjection\ContainerInterface,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\Security\Core\Authentication\Token\TokenInterface,
    Symfony\Component\Security\Http\HttpUtils;

/**
 * ServiceMap. Holds several services for a firewall.
 *
 */
class ServiceProvider
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

    /**
     * Constructor.
     *
     * @param ContainerInterface $container              Container used to lazy load the resource owners.
     * @param array              $services         Array with configured resource owners.
     */
    public function __construct($services)
    {
        $this->services         = $services;
    }

    /**
     * Gets the appropriate resource owner given the name.
     *
     * @param string $name
     *
     * @return null|\Acts\ExternalLoginBundle\Security\Service\ServiceInterface
     */
    public function getServiceByName($name)
    {
        if (!isset($this->services[$name])) {
            return null;
        }

        return $this->services[$name];
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