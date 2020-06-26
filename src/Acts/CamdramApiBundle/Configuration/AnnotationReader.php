<?php
namespace Acts\CamdramApiBundle\Configuration;

use Acts\CamdramApiBundle\Configuration\Annotation\Feed;
use Acts\CamdramApiBundle\Configuration\Annotation\Link;
use Acts\CamdramApiBundle\Configuration\ApiData;
use Acts\CamdramApiBundle\Configuration\LinkMetadata;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;

class AnnotationReader
{
    private $reader;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Reader $reader, EntityManagerInterface $em)
    {
        $this->reader = $reader;
        $this->em = $em;
    }

    /**
     * @throws \Acts\CamdramApiBundle\Exception\UnsupportedTypeException
     */
    public function read($object): ApiData
    {
        $data = new ApiData();

        if (!is_object($object)) {
            return $data;
        }

        $reflection = new \ReflectionObject($object);

        $annotation = $this->reader->getClassAnnotation($reflection, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Feed');
        if ($annotation instanceof Feed) {
            $data->setFeed($annotation);
        }
        $annotation = $this->reader->getClassAnnotation($reflection, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Link');
        if ($annotation instanceof Link) {
            $link = $this->createLinkMetadataFromAnnoation($annotation);
            $link->setName('self');
            $targetType = strtolower($reflection->getShortName());
            $link->setEntity($targetType);
            $data->setSelfLink($link);
        }

        //Be careful not to throw exceptions when visiting non-entity classes
        $doctrineMetadata = $this->em->getMetadataFactory()->isTransient($reflection->getName())
            ? null : $this->em->getClassMetadata($reflection->getName());

        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Link');
            if ($annotation instanceof Link) {
                $link = $this->createLinkMetadataFromAnnoation($annotation);

                $link->setProperty($property->getName());
                if (!$link->getName()) {
                    $link->setName($property->getName());
                }
                if (!$link->getEntity() && $doctrineMetadata) {
                    $mapping = $doctrineMetadata->getAssociationMapping($property->getName());

                    $targetType = strtolower((new \ReflectionClass($mapping['targetEntity']))->getShortName());
                    $link->setEntity($targetType);
                }

                $data->addLink($link);
            }
        }
        foreach ($reflection->getMethods() as $method) {
            $annotation = $this->reader->getMethodAnnotation($method, 'Acts\\CamdramApiBundle\\Configuration\\Annotation\\Link');
            if ($annotation instanceof Link) {
                $link = $this->createLinkMetadataFromAnnoation($annotation);
                $link->setProperty($method->getName());
                if (!$link->getName()) {
                    $link->setName($method->getName());
                }

                $data->addLink($link);
            }
        }

        return $data;
    }

    private function createLinkMetadataFromAnnoation(Link $annotation): LinkMetadata
    {
        $link = new LinkMetadata();
        $link->setName($annotation->getName());
        $link->setEmbed($annotation->isEmbed());
        $link->setRoute($annotation->getRoute());
        $link->setParams($annotation->getParams());
        $link->setEntity($annotation->getTargetType());
        return $link;
    }
}
