<?php

namespace Acts\CamdramApiBundle\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Doctrine\Common\Inflector\Inflector;

class EntityUrlGenerator
{
    private $router;

    private static $class_map = array(
        'TechieAdvert' => 'techie'
    );

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    private function getRouteResourceName($class)
    {
        $class = new \ReflectionClass($class);
        $className = $class->getShortName();
        if (isset(self::$class_map[$className])) {
            return self::$class_map[$className];
        } else {
            return strtolower($className);
        }
    }

    public function getCollectionRoute($class)
    {
        $route = 'get_' . Inflector::pluralize($this->getRouteResourceName($class));
        if ($this->router->getRouteCollection()->get($route) === null) {
            throw new \InvalidArgumentException('That entity does not have a corresponding collection route');
        }

        return $route;
    }

    public function getRoute($entity)
    {
        $route = 'get_' . $this->getRouteResourceName($entity);
        if ($this->router->getRouteCollection()->get($route) === null) {
            throw new \InvalidArgumentException('That entity does not have a corresponding route');
        }

        return $route;
    }

    public function getIdentifier($entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        try {
            $id = $accessor->getValue($entity, 'slug');
        } catch (NoSuchPropertyException $e) {
            $id = $accessor->getValue($entity, 'id');
        }

        return $id;
    }

    public function generateUrl($entity, $format = null)
    {
        return $this->router->generate($this->getRoute($entity), array(
            'identifier' => $this->getIdentifier($entity),
            '_format'     => $format
        ), true);
    }

    public function generateCollectionUrl($class, $format = null)
    {
        return $this->router->generate($this->getCollectionRoute($class), array('_format' => $format), true);
    }
    
    public function getDefaultUrl()
    {
        return $this->router->generate('acts_camdram_homepage');
    }
}
