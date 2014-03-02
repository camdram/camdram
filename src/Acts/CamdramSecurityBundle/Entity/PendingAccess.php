<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Acts\CamdramSecurityBundle\Entity\User;

/**
 * PendingAccess
 *
 * PendingAccess acts as an Access Control Entry (ACE) in the case where the
 * user doesn't have an account on Camdram. In a typical case, a show is entered
 * on Camdram by a society or venue administrator. Pending access entries are
 * created when a show-specific admin doesn't have an account on Camdram
 * (determined by the given email address). The person is prompted
 * @ORM\Table(name="acts_pendingaccess")
 * @ORM\Entity(repositoryClass="PendingAccessRepository")
 * @ORM\EntityListeners({"\Acts\CamdramSecurityBundle\EventListener\PendingAccessListener"})
 * @ORM\HasLifecycleCallbacks()
 */
class PendingAccess
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
     * @ORM\Column(name="rid", type="integer", nullable=false)
     */
    private $rid;

    /**
     * @var string
     *
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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramSecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="issuerid", referencedColumnName="id", nullable=false)
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set rid
     *
     * @param integer $rid
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
     * @return integer
     */
    public function getRid()
    {
        return $this->rid;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return PendingAccess
     */
    public function setEmail($email)
    {
        /* Store emails in a Camdram v1 compatible way, i.e. strip cam.ac.uk
         * and hermes.cam.ac.uk suffixes. This modification is identical to the
         * EmailtoUser function in v1 as a result.
         */
        $email=ereg_replace("[^[:alnum:]@.+-]", "", $email);
        $email=ereg_replace("@cam.ac.uk","",$email);
        $email=ereg_replace("@hermes.cam.ac.uk","",$email);
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
        /* Add missing suffix, if required. */
        $email = $this->email;
        if (($email != '') && !strchr($email,'@')) {
            $email .= "@cam.ac.uk";
        }
        return $email;
    }

    /**
     * Set type
     *
     * @param string $type
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

    /**
     * Set issuer_id
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $owner
     * @return PendingAccess
     */
    public function setIssuer(\Acts\CamdramSecurityBundle\Entity\User $issuer = null)
    {
        $this->issuer= $issuer;

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
