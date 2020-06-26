<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PendingAccess
 *
 * PendingAccess acts as an Access Control Entry (ACE) in the case where the
 * user doesn't have an account on Camdram. In a typical case, a show is entered
 * on Camdram by a society or venue administrator. Pending access entries are
 * created when a show-specific admin doesn't have an account on Camdram
 * (determined by the given email address). The person is prompted
 *
 * @ORM\Table(name="acts_pendingaccess")
 * @ORM\Entity(repositoryClass="PendingAccessRepository")
 * @ORM\EntityListeners({"\Acts\CamdramSecurityBundle\EventListener\PendingAccessListener"})
 * @ORM\HasLifecycleCallbacks()
 */
class PendingAccess
{
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
     * @ORM\Column(name="rid", type="integer", nullable=false)
     */
    private $rid;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Email
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramSecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="issuerid", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $issuer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creationdate", type="date", nullable=false)
     */
    private $creation_date;

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
     * Set rid
     *
     * @param int $rid
     *
     * @return PendingAccess
     */
    public function setRid($rid)
    {
        $this->rid = $rid;

        return $this;
    }

    /**
     * Get rid
     *
     * @return int
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return PendingAccess
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);

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
     * Set type
     *
     * @param string $type
     *
     * @return PendingAccess
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setIssuer(\Acts\CamdramSecurityBundle\Entity\User $issuer = null): self
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return \Acts\CamdramSecurityBundle\Entity\User $issuer
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Set creation_date
     *
     * This should only be called by Doctrine during PrePersit, but must be
     * declared as a public function.
     *
     * @return PendingAccess
     * @ORM\PrePersist()
     */
    public function setCreationDate()
    {
        $this->creation_date = new \DateTime();

        return $this;
    }

    /**
     * Get creation_date
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }
}
