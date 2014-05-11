<?php

namespace Acts\CamdramLegacyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Catalogue
 *
 * @ORM\Table(name="acts_catalogue")
 * @ORM\Entity
 */
class Catalogue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="storeid", type="integer", nullable=false)
     */
    private $store_id;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set store_id
     *
     * @param integer $storeId
     * @return Catalogue
     */
    public function setStoreId($storeId)
    {
        $this->store_id = $storeId;

        return $this;
    }

    /**
     * Get store_id
     *
     * @return integer
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Catalogue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
