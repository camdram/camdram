<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessControlEntry
 *
 * @ORM\Table(name="acts_access")
 * @ORM\Entity(repositoryClass="AccessControlEntryRepository")
 */
class AccessControlEntry
{
    const LEVEL_FULL_ADMIN = -1;
    const LEVEL_ADMIN = -2;
    const LEVEL_CONTENT_ADMIN = -3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="string", length=20)
     */
    private $type;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="aces")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $user;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="ace_grants")
     * @ORM\JoinColumn(name="granted_by_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $grantedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     *
     * @return AccessControlEntry
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set user
     *
     * @param User $user
     *
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
     *
     * @return AccessControlEntry
     */
    public function setGrantedBy(User $grantedBy = null)
    {
        $this->grantedBy = $grantedBy;

        return $this;
    }

    /**
     * Get granted_by
     *
     * @return User
     */
    public function getGrantedBy()
    {
        return $this->grantedBy;
    }

    /**
     * Set type
     *
     * @param int $type
     *
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
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set entity_id
     *
     * @param int $entityId
     *
     * @return AccessControlEntry
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entity_id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

}
