<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * @ORM\Table(name="acts_position_tags")
 * @ORM\Entity(repositoryClass=PositionTagRepository::class)
 * @Serializer\XmlRoot("tag")
 */
class PositionTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Position", inversedBy="tags")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition() : ?Position
    {
        return $this->position;
    }

    public function setPosition(?Position $position) : self
    {
        $this->position = $position;

        return $this;
    }
}
