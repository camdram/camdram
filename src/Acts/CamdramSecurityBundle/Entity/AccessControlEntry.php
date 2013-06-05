<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Access
 *
 * @ORM\Table(name="acts_access_control_entries")
 * @ORM\Entity(repositoryClass="AccessControlEntryRepository")
 */
class AccessControlEntry
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
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\Entity")
     */
    private $entity;

    /**
     * @var \Acts\CamdramBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $user_id;

    /**
     * @var Group
     *
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * })
     */
    private $group;

    /**
     * @var int
     *
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     */
    private $group_id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", nullable=true)
     */
    private $email;

    /**
     * @var \Acts\CamdramBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="granted_by_id", referencedColumnName="id")
     * })
     */
    private $granted_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date", nullable=false)
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="granted_at", type="date", nullable=false)
     */
    private $granted_at;

    /**
     * @var \Acts\CamdramBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revoked_by_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     * })
     */
    private $revoked_by;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revoked_at", type="date", nullable=true)
     */
    private $revoked_at;


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
     * Set entity
     *
     * @param \Acts\CamdramBundle\Entity\Entity $entity
     * @return AccessControlEntry
     */
    public function setEntity(\Acts\CamdramBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return \Acts\CamdramBundle\Entity\Entity 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return AccessControlEntry
     */
    public function setUser(\Acts\CamdramBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set granted_by
     *
     * @param \Acts\CamdramBundle\Entity\User $grantedBy
     * @return AccessControlEntry
     */
    public function setGrantedBy(\Acts\CamdramBundle\Entity\User $grantedBy = null)
    {
        $this->granted_by = $grantedBy;
    
        return $this;
    }

    /**
     * Get granted_by
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getGrantedBy()
    {
        return $this->granted_by;
    }

    /**
     * Set revoker
     *
     * @param \Acts\CamdramBundle\Entity\User $revoker
     * @return AccessControlEntry
     */
    public function setRevokedBy(\Acts\CamdramBundle\Entity\User $revoker = null)
    {
        $this->revoked_by = $revoker;
    
        return $this;
    }

    /**
     * Get revoker
     *
     * @return \Acts\CamdramBundle\Entity\User 
     */
    public function getRevokedBy()
    {
        return $this->revoked_by;
    }

    /**
     * Set group
     *
     * @param \Acts\CamdramBundle\Entity\Group $group
     * @return AccessControlEntry
     */
    public function setGroup(\Acts\CamdramSecurityBundle\Entity\Group $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \Acts\CamdramSecurityBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AccessControlEntry
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
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
     * Set group_id
     *
     * @param integer $groupId
     * @return AccessControlEntry
     */
    public function setGroupId($groupId)
    {
        $this->group_id = $groupId;

        return $this;
    }

    /**
     * Get group_id
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->group_id;
    }
}
