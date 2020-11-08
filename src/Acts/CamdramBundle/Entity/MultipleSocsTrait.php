<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Trait which holds the logic and properties needed to manage multiple societies.
 * Requires a $societies property on the target entity.
 */
trait MultipleSocsTrait
{
    /**
     * A JSON representation of how the show's societies should be displayed,
     * for the purpose of storing unregistered societies and how they are
     * ordered with registered societies.
     * NOT used for access control etc.
     * ["New Society", 12] might be rendered as
     *     New Society and Cambridge Footlights present...
     * assuming the Footlights have id 12.
     *
     * @var array
     *
     * @ORM\Column(name="socs_list", type="json_array", nullable=false)
     * @Gedmo\Versioned
     * @Serializer\Expose(if="object.shouldSerializeSocieties()")
     */
    private $societies_display_list = [];
    /**
     * It's advisable to use getPrettySocData instead as it has the definitive
     * handling of inconsistencies between societies (i.e. the join table) and
     * this.
     */
    public function getSocietiesDisplayList()
    {
        return $this->societies_display_list;
    }

    /**
     * The correct way to access societies in the API.
     * @Serializer\VirtualProperty()
     * @Serializer\XmlKeyValuePairs()
     * @Serializer\SerializedName("societies")
     * @Serializer\Expose(if="object.shouldSerializeSocieties()")
     */
    public function getSocietiesForAPI()
    {
        $data = $this->getPrettySocData();
        return array_map(function($s) {
            return is_array($s) ? $s : ["id" => $s->getId(), "name" => $s->getName(), "slug" => $s->getSlug()];
        }, $data);
    }

    /**
     * @param array $societiesList
     */
    public function setSocietiesDisplayList($societiesList): self
    {
        $this->societies_display_list = $societiesList;

        return $this;
    }

    /**
     * Gets all relevant data on societies ready for display to the user;
     * returns an array of arrays [ "name" => "Some Small Soc" ] or of Societies.
     * Uses societies_display_list for ordering but gives priority to the info
     * in societies.
     */
    public function getPrettySocData(): array {
        $data = $this->societies_display_list;
        $out = array();
        foreach ($data as $soc_basic) {
            if (is_string($soc_basic)) {
                $out[] = ["name" => $soc_basic];
            } else if (is_numeric($soc_basic)) {
                # is_numeric would return true for the STRING "1234", so the if
                # statements have to be in this order.
                foreach ($this->societies as $s) {
                    if ($s->getId() == $soc_basic) {
                        $out[] = $s;
                        break;
                    }
                }
            }
        }
        foreach ($this->societies as $society) {
            if (!in_array($society->getId(), $data, true)) {
                $out[] = $society;
            }
        }
        return $out;
    }

    /**
     * Get societies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSocieties()
    {
        return $this->societies;
    }

    public function addSociety(Society $society): self
    {
        if (!$this->societies->contains($society)) {
            $this->societies[] = $society;
        }

        return $this;
    }

    public function removeSociety(Society $society): self
    {
        if ($this->societies->contains($society)) {
            $this->societies->removeElement($society);
        }

        return $this;
    }

    public function shouldSerializeSocieties(): bool
    {
        return true;
    }
}
