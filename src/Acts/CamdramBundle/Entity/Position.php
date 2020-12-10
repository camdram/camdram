<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * @ORM\Table(name="acts_positions")
 * @ORM\Entity(repositoryClass=PositionRepository::class)
 * @Serializer\XmlRoot("position")
 * @Api\Link(route="get_position", params={"identifier": "object.getSlug()"})
 */
class Position
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="primary_name", type="string", length=255)
     */
    private $primaryName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"primaryName"})
     */
    private $slug;

    /**
     * @ORM\Column(name="wiki_name", type="string", length=255)
     */
    private $wikiName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrimaryName() : ?string
    {
        return $this->primaryName;
    }

    public function setPrimaryName(?string $primaryName) : self
    {
        $this->primaryName = $primaryName;

        return $this;
    }

    public function getSlug() : ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug) : self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getWikiName() : ?string
    {
        return $this->wikiName;
    }

    public function setWikiName(?string $wikiName) : self
    {
        $this->wikiName = $wikiName;

        return $this;
    }
}
