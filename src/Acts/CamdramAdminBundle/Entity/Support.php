<?php

namespace Acts\CamdramAdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramSecurityBundle\Entity\User;

/**
 * Support
 *
 * @ORM\Table(name="acts_support")
 * @ORM\Entity(repositoryClass="Acts\CamdramAdminBundle\Entity\SupportRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Support
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
     * @var string
     *
     * @ORM\Column(name="`from`", type="string", nullable=false)
     */
    private $from;

    /**
     * @var string
     *
     * @ORM\Column(name="`to`", type="string", nullable=false)
     */
    private $to;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="string", nullable=false)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=false)
     */
    private $body;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramSecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="ownerid", referencedColumnName="id", nullable=true)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=20, nullable=false)
     */
    private $state = 'unassigned';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datetime", type="datetime", nullable=false)
     */
    private $date_time;

    /**
     * @ORM\OneToMany(targetEntity="Support", mappedBy="parent")
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="Support", inversedBy="children")
     * @ORM\JoinColumn(name="supportid", referencedColumnName="id", nullable=true)
     */
    private $parent;

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get children
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

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
     * Set from
     *
     * @param string $from
     * @return Support
     */
    public function setFrom($from)
    {
        $this->from = htmlspecialchars($from);

        return $this;
    }

    /**
     * Get from
     *
     * @return string
     */
    public function getFrom()
    {
        return htmlspecialchars_decode($this->from);
    }

    /**
     * Set to. HTML special characters are automatically [un]escaped when
     * getting or setting.
     *
     * @param string $to
     * @return Support
     */
    public function setTo($to)
    {
        $this->to = htmlspecialchars($to);

        return $this;
    }

    /**
     * Get to
     *
     * @return string
     */
    public function getTo()
    {
        return htmlspecialchars_decode($this->to);
    }

    /**
     * Set cc
     *
     * @param string $cc
     * @return Support
     */
    public function setCc($cc)
    {
        $this->cc = htmlspecialchars($cc);

        return $this;
    }

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc()
    {
        return htmlspecialchars_decode($this->cc);
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Support
     */
    public function setSubject($subject)
    {
        $this->subject = htmlspecialchars($subject);

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return htmlspecialchars_decode($this->subject);
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Support
     */
    public function setBody($body)
    {
        $this->body = str_replace(chr(13), "", $body);

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set owner
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $owner
     * @return Support
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Acts\CamdramSecurityBundle\Entity\User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set state
     * state for issues should be one of [unassigned, assigned, closed]
     *
     * @param string $state
     * @return Support
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set date_time
     *
     * @return Support
     * @ORM\PrePersist()
     */
    public function setDateTime()
    {
        $this->date_time = new \DateTime('now');

        return $this;
    }

    /**
     * Get date_time
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * Add children
     *
     * @param Support $children
     * @return Support
     */
    public function addChildren(Support $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Acts\CamdramBundle\Entity\Support $children
     */
    public function removeChildren(Support $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Set parent
     *
     * @param \Acts\CamdramBundle\Entity\Support $parent
     * @return Support
     */
    public function setParent(Support $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Support
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param Support $children
     * @return Support
     */
    public function addChild(Support $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Support $children
     */
    public function removeChild(Support $children)
    {
        $this->children->removeElement($children);
    }

    public function getOriginal()
    {
        if ($this->getParent()) {
            return $this->getParent()->getOriginal();
        } else {
            return $this;
        }
    }

    public function getOriginalFrom()
    {
        return $this->getOriginal()->getFrom();
    }

    public function getOriginalId()
    {
        return $this->getOriginal()->getId();
    }
}
