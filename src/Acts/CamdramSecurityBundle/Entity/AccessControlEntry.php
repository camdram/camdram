<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessControlEntry
 *
 * @ORM\Table(name="acts_access")
 * @ORM\Entity(repositoryClass="AccessControlEntryRepository")
 * @ORM\EntityListeners({"\Acts\CamdramSecurityBundle\EventListener\AccessControlEntryListener"})
 */
class AccessControlEntry
{
    const LEVEL_FULL_ADMIN = -1;
    const LEVEL_ADMIN = -2;
    const LEVEL_CONTENT_ADMIN = -3;

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
     * @ORM\Column(name="rid", type="integer")
     */
    private $entity_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="aces")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="uid", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issuerid", referencedColumnName="id")
     * })
     */
    private $granted_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $created_at;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revokeid", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $revoked_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revokedate", type="date", nullable=true)
     */
    private $revoked_at;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact", type="boolean")
     */
    private $contact = 0;


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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return AccessControlEntry
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set granted_at
     *
     * @param \DateTime $grantedAt
     * @return AccessControlEntry
     */
    public function setGrantedAt($grantedAt)
    {
        $this->granted_at = $grantedAt;
    
        return $this;
    }

    /**
     * Get granted_at
     *
     * @return \DateTime 
     */
    public function getGrantedAt()
    {
        return $this->granted_at;
    }

    /**
     * Set revoked_at
     *
     * @param \DateTime $revokedAt
     * @return AccessControlEntry
     */
    public function setRevokedAt($revokedAt)
    {
        $this->revoked_at = $revokedAt;
    
        return $this;
    }

    /**
     * Get revoked_at
     *
     * @return \DateTime 
     */
    public function getRevokedAt()
    {
        return $this->revoked_at;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return AccessControlEntry
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set granted_by
     *
     * @param User $grantedBy
     * @return AccessControlEntry
     */
    public function setGrantedBy(User $grantedBy = null)
    {
        $this->granted_by = $grantedBy;
    
        return $this;
    }

    /**
     * Get granted_by
     *
     * @return User
     */
    public function getGrantedBy()
    {
        return $this->granted_by;
    }

    /**
     * Set revoker
     *
     * @param User $revoker
     * @return AccessControlEntry
     */
    public function setRevokedBy(User $revoker = null)
    {
        $this->revoked_by = $revoker;
    
        return $this;
    }

    /**
     * Get revoker
     *
     * @return User 
     */
    public function getRevokedBy()
    {
        return $this->revoked_by;
    }

    /**
     * Set user_id
     *
     * @param integer $userId
     * @return AccessControlEntry
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return AccessControlEntry
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set entity_id
     *
     * @param integer $entityId
     * @return AccessControlEntry
     */
    public function setEntityId($entityId)
    {
        $this->entity_id = $entityId;
    
        return $this;
    }

    /**
     * Get entity_id
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entity_id;
    }

    /**
     * Set contact
     *
     * @param boolean $contact
     * @return AccessControlEntry
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    
        return $this;
    }

    /**
     * Get contact
     *
     * @return boolean 
     */
    public function getContact()
    {
        return $this->contact;
    }
}
