<?php
namespace Acts\CamdramApiBundle\Configuration;

use Acts\CamdramApiBundle\Configuration\Annotation\Feed;
use Acts\CamdramApiBundle\Configuration\Annotation\Link;
use Acts\CamdramApiBundle\Configuration\ApiData;
use Acts\CamdramApiBundle\Configuration\LinkMetadata;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;

class AnnotationReader {

    private $reader;

    private $em;

    public function __construct(Reader $reader, EntityManager $em)
    {
        $this->reader = $reader;
        $this->em = $em;
    }

    /**
     * @param $object
     * @return \Acts\CamdramApiBundle\Configuration\ApiData
     * @throws \Acts\CamdramApiBundle\Exception\UnsupportedTypeException
     */
    public function read($object)
    {
        $reflection = new \ReflectionObject($object);
        $data = new ApiData();

        $annotation = $this->reader->getClassAnnotation($reflection, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Feed');
        if ($annotation instanceof Feed) {
            $data->setFeed($annotation);
        }
        $doctrineMetadata = $this->em->getClassMetadata($reflection->getName());

        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Link');
            if ($annotation instanceof Link) {
                $link = new LinkMetadata();
                $link->setProperty($property->getName());
                $link->setName($annotation->getName() ?: $property->getName());
                $link->setEmbed($annotation->isEmbed());
                $link->setRoute($annotation->getRoute());
                $link->setParams($annotation->getParams());

                $mapping = $doctrineMetadata->getAssociationMapping($property->getName());
                $link->setEntity($mapping['targetEntity']);

                $data->addLink($link);
            }
        }

        return $data;
    }
} 