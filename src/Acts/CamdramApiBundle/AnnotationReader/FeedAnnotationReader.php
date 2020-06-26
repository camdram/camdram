<?php

namespace Acts\CamdramApiBundle\AnnotationReader;

use Doctrine\Common\Annotations\Reader;
use Acts\CamdramApiBundle\Configuration\Annotation\Feed;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;

class FeedAnnotationReader
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @throws \Acts\CamdramApiBundle\Exception\UnsupportedTypeException
     */
    public function read($object): Feed
    {
        $reflection = new \ReflectionObject($object);

        $annotation = $this->reader->getClassAnnotation($reflection, 'Acts\\CamdramApiBundle\\Annotation\Feed');

        if (!$annotation instanceof Feed) {
            throw new UnsupportedTypeException(get_class($object), 'feed');
        }

        return $annotation;
    }
}
