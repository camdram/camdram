<?php
namespace Acts\SocialApiBundle\Service;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiProvider
{
    /**
     * An array of registered apis
     *
     * @var array
     */
    private $api_names;

    /**
     * The service container - only used to retrieve our own api services
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, array $api_names)
    {
        $this->container = $container;
        $this->api_names = $api_names;
    }

    public function exists($api_name)
    {
        foreach ($this->api_names as $name) {
            if ($name == $api_name) return true;
        }
        return false;
    }

    public function getNames()
    {
        return $this->api_names;
    }

    public function get($name)
    {
        if (!$this->exists($name)) {
            throw new \InvalidArgumentException('The api "'.$name.'" does not exist');
        }
        return $this->container->get('acts.social_api.apis.'.$name);
    }

}