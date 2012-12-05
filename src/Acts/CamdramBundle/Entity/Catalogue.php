<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsCatalogue
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
    private $storeid;

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
     * Set storeid
     *
     * @param integer $storeid
     * @return ActsCatalogue
     */
    public function setStoreid($storeid)
    {
        $this->storeid = $storeid;
    
        return $this;
    }

    /**
     * Get storeid
     *
     * @return integer 
     */
    public function getStoreid()
    {
        return $this->storeid;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ActsCatalogue
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