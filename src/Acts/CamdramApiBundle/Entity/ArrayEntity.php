<?php

namespace Acts\CamdramApiBundle\Entity;

use JMS\Serializer\Annotation as Serializer;

/**
 * This class permits allows arrays with string keys to be presented usefully
 * in the API as both XML and JSON.
 */
class ArrayEntity {
    /**
     * @Serializer\Inline
     * @Serializer\XmlAttributeMap
     * @var array<string,mixed>
     */
    public $attrs = [];

    /**
     * @param array<string,mixed> $attrs
     */
    public function __construct(array $attrs) {
        $this->attrs = $attrs;
    }
}
