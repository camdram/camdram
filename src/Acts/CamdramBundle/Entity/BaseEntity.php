<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/** @ORM\MappedSuperclass */
abstract class BaseEntity {
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\XmlAttribute
     * @Serializer\Expose()
     * @Serializer\Type("integer")
     */
    protected $id;

    abstract public function getId(): ?int;
    abstract public function getName(): ?string;
    abstract public function getSlug(): ?string;
    abstract public function getEntityType(): string;
}
