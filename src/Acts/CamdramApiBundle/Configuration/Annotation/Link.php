<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 03/01/15
 * Time: 21:08
 */

namespace Acts\CamdramApiBundle\Configuration\Annotation;

/**
 * Class Link
 *
 * An annotation used during serialization to denote a link to an external resource
 *
 * @Annotation
 * @package Acts\CamdramApiBundle\Annotation
 */
class Link {

    /**
     * @var string
     */
    private $name;

    /**
     * @var boolean
     */
    private $embed = false;

    /**
     * @var string
     */
    private $route;

    /**
     * @var array
     */
    private $params;

    public function __construct(array $values)
    {
        $this->name = isset($values['name']) ? $values['name'] : null;
        $this->embed = isset($values['embed']) ? $values['embed'] : false;
        $this->route = $values['route'];
        $this->params = isset($values['params']) ? $values['params'] : array();
    }

    /**
     * @return boolean
     */
    public function isEmbed()
    {
        return $this->embed;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

} 