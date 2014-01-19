<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * News
 *
 * @ORM\Table(name="acts_news_mentions")
 * @ORM\Entity
 */
class NewsMention
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /** @var News
     *
     * @ORM\ManyToOne(targetEntity="News", inversedBy="mentions")
     */
    private $news;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="remote_id", type="string", length=255, nullable=false)
     */
    private $remote_id;

    /** @var Entity
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     */
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=20, nullable=false)
     */
    private $service;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="integer")
     */
    private $offset;

    /**
     * @var integer
     *
     * @ORM\Column(name="length", type="integer")
     */
    private $length;


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
     * Set name
     *
     * @param string $name
     * @return NewsMention
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set remote_id
     *
     * @param string $remoteId
     * @return NewsMention
     */
    public function setRemoteId($remoteId)
    {
        $this->remote_id = $remoteId;
    
        return $this;
    }

    /**
     * Get remote_id
     *
     * @return string 
     */
    public function getRemoteId()
    {
        return $this->remote_id;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return NewsMention
     */
    public function setService($service)
    {
        $this->service = $service;
    
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set offset
     *
     * @param integer $offset
     * @return NewsMention
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    
        return $this;
    }

    /**
     * Get offset
     *
     * @return integer 
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set length
     *
     * @param integer $length
     * @return NewsMention
     */
    public function setLength($length)
    {
        $this->length = $length;
    
        return $this;
    }

    /**
     * Get length
     *
     * @return integer 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set news
     *
     * @param \Acts\CamdramBundle\Entity\News $news
     * @return NewsMention
     */
    public function setNews(\Acts\CamdramBundle\Entity\News $news = null)
    {
        $this->news = $news;
    
        return $this;
    }

    /**
     * Get news
     *
     * @return \Acts\CamdramBundle\Entity\News 
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * Set entity
     *
     * @param \Acts\CamdramBundle\Entity\Organisation $entity
     * @return NewsMention
     */
    public function setEntity(\Acts\CamdramBundle\Entity\Organisation $entity = null)
    {
        $this->entity = $entity;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return \Acts\CamdramBundle\Entity\Organisation
     */
    public function getEntity()
    {
        return $this->entity;
    }
}