<?php
namespace Acts\CamdramApiBundle\AnnotationReader;

use Doctrine\Common\Annotations\Reader;
use Acts\CamdramApiBundle\Annotation\Feed;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;

class FeedAnnotationReader
{
    private $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $object
     * @return \Acts\CamdramApiBundle\Annotation\Feed
     * @throws \Acts\CamdramApiBundle\Exception\UnsupportedTypeException
     */
    public function read($object)
    {
        $reflection = new \ReflectionObject($object);

        $annotation = $this->reader->getClassAnnotation($reflection, 'Acts\\CamdramApiBundle\\Annotation\Feed');

        if (!$annotation instanceof Feed) {
            throw new UnsupportedTypeException(get_class($object), 'feed');
        }

        return $annotation;
    }
}