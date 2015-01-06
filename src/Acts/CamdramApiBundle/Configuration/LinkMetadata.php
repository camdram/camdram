<?php
namespace Acts\CamdramApiBundle\Configuration;

class LinkMetadata {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $embed;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $params;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * @return string
     */
    public function getEmbed()
    {
        return $this->embed;
    }

    /**
     * @param string $embed
     */
    public function setEmbed($embed)
    {
        $this->embed = $embed;
    }

    /**
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getShortEntity()
    {
        return strtolower((new \ReflectionClass($this->entity))->getShortName());
    }

    /**
     * @param string $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

} 