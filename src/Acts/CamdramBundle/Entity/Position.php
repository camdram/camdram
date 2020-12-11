<?php
namespace Acts\CamdramBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(name="wiki_name", type="string", length=255, nullable=true)
     */
    private $wikiName;

    /**
     * @ORM\OneToMany(targetEntity="PositionTag", mappedBy="position", cascade={"all"}, orphanRemoval=true)
     * @Serializer\XmlList(inline = true, entry = "tag")
     */
    private $tags;

    /**
     * @var Collection<Advert>
     * @ORM\ManyToMany(targetEntity="Advert", mappedBy="positions")
     */
    private $adverts;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->adverts = new ArrayCollection();
    }

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

    /**
     * @return Collection|PositionTag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(PositionTag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setPosition($this);
        }

        return $this;
    }

    public function addTagName(?string $name) : self
    {
        $tag = new PositionTag;
        $tag->setName($name);

        return $this->addTag($tag);
    }

    public function removeTag(PositionTag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            // set the owning side to null (unless already changed)
            if ($tag->getPosition() === $this) {
                $tag->setPosition(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Advert[]
     */
    public function getAdverts(): Collection
    {
        return $this->adverts;
    }

    public function addAdvert(Advert $advert): self
    {
        if (!$this->adverts->contains($advert)) {
            $this->adverts[] = $advert;
        }

        return $this;
    }

    public function removeAdvert(Advert $advert): self
    {
        $this->adverts->removeElement($advert);

        return $this;
    }
}
