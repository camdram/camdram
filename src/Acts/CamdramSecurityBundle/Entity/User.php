<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramBundle\Entity\Person;
use Symfony\Component\Security\Core\User\UserInterface;
use Acts\CamdramApiBundle\Configuration\Annotation as Api;

/**
 * User
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\EntityListeners({"Acts\CamdramSecurityBundle\EventListener\UserListener" })
 * @UniqueEntity(fields="email", message="An account already exists with that email address")
 * @Serializer\XmlRoot("user")
 * @Api\Link(route="get_account")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Groups({"all"})
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Groups({"all"})
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Assert\Email()
     * @Serializer\Groups({"user_email"})
     */
    private $email;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registered_at;

    /**
     * The last time the user underwent an explicit login flow
     *
     * @var ?\DateTime
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    private $last_login_at;

    /**
     * The last time a session was created for the user
     *
     * @var ?\DateTime
     * @ORM\Column(name="last_session_at", type="datetime", nullable=true)
     */
    private $last_session_at;

    /**
     * @var ?Person
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\Person", inversedBy="users")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $person;

    /**
     * @var \Doctrine\Common\Collections\Collection<int|string,ExternalUser>
     * @ORM\OneToMany(targetEntity="Acts\CamdramSecurityBundle\Entity\ExternalUser", mappedBy="user", cascade={"remove"})
     * @Serializer\Exclude()
     */
    private $external_users;

    /**
     * @var bool
     * @ORM\Column(name="is_email_verified", type="boolean")
     */
    private $is_email_verified = false;

    /**
     * @var ?string
     *
     * @ORM\Column(name="profile_picture_url", type="string", nullable=true)
     */
    private $profile_picture_url;

    /**
     * @var \Doctrine\Common\Collections\Collection<int|string,AccessControlEntry>
     * @ORM\OneToMany(targetEntity="Acts\CamdramSecurityBundle\Entity\AccessControlEntry", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $aces;

    /**
     * @Api\Link(route="get_account_shows", targetType="Acts\\CamdramBundle\\Entity\\Show")
     */
    private $shows;

    /**
     * @Api\Link(route="get_account_organisations", targetType="Acts\\CamdramBundle\\Entity\\Organisation")
     */
    private $organisations;

    /**
     * @var \Doctrine\Common\Collections\Collection<int|string,AccessControlEntry>
     * @ORM\OneToMany(targetEntity="AccessControlEntry", mappedBy="grantedBy")
     * @Serializer\Exclude()
     */
    private $ace_grants;

    /**
     * @var \Doctrine\Common\Collections\Collection<int|string,\Acts\CamdramApiBundle\Entity\Authorization>
     * @ORM\OneToMany(targetEntity="Acts\CamdramApiBundle\Entity\Authorization", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $authorizations;

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
     * Set name
     *
     * @param string $name
     *
     * @return User
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

    /** @return string */
    public function getDisplayName()
    {
        return $this->getName();
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
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
     * Set registered
     *
     * @param \DateTime $registered_at
     *
     * @return User
     */
    public function setRegisteredAt($registered_at)
    {
        $this->registered_at = $registered_at;

        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registered_at;
    }

    public function setLastLoginAt(?\DateTime $last_login_at): self
    {
        $this->last_login_at = $last_login_at;

        return $this;
    }

    /**
     * Get login
     *
     * @return ?\DateTime
     */
    public function getLastLoginAt()
    {
        return $this->last_login_at;
    }

    public function setLastSessionAt(?\DateTime $last_session_at): self
    {
        $this->last_session_at = $last_session_at;

        return $this;
    }

    /**
     * Get last_session_at
     *
     * @return \DateTime
     */
    public function getLastSessionAt()
    {
        return $this->last_session_at;
    }

    /**
     * Get password. Required for UserInterface
     *
     * @return string
     */
    public function getPassword()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getSalt()
    {
        return '';
    }

    public function getRoles()
    {
        $roles = array('ROLE_USER');

        foreach ($this->getSecurityAces() as $ace) {
            switch ($ace->getEntityId()) {
                case -1: $roles[] = 'ROLE_SUPER_ADMIN'; break;
                case -2: $roles[] = 'ROLE_ADMIN'; break;
                case -3: $roles[] = 'ROLE_EDITOR'; break;
            }
        }

        return $roles;
    }

    public function eraseCredentials()
    {
    }

    public function setPerson(Person $person = null): self
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \Acts\CamdramBundle\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->registered_at = new \DateTime();
        $this->last_login_at = new \DateTime();
        $this->last_session_at = new \DateTime();

        $this->aces = new ArrayCollection();
        $this->external_users = new ArrayCollection();
    }

    public function serialize()
    {
        return serialize(array(
                $this->id, $this->name, $this->email
        ));
    }
    public function unserialize($serialized)
    {
        list($this->id, $this->name, $this->email) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getName().' ('.$this->getEmail().')';
    }

    public function addExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUser): self
    {
        $this->external_users[] = $externalUser;

        if (!$this->getProfilePictureUrl()) {
            $this->setProfilePictureUrl($externalUser->getProfilePictureUrl());
        }

        return $this;
    }

    public function removeExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUser): void
    {
        $this->external_users->removeElement($externalUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int|string,ExternalUser>
     */
    public function getExternalUsers()
    {
        return $this->external_users;
    }

    public function getExternalUserByService($service)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('service', $service));
        $res = $this->external_users->matching($criteria);
        if (count($res) > 0) {
            return $res->first();
        }
    }

    public function getType(): string
    {
        return 'native';
    }

    public function setIsEmailVerified(bool $isEmailVerified): self
    {
        $this->is_email_verified = $isEmailVerified;

        return $this;
    }

    public function getIsEmailVerified(): bool
    {
        return $this->is_email_verified;
    }

    public function setProfilePictureUrl(?string $profilePictureUrl): self
    {
        $this->profile_picture_url = $profilePictureUrl;
        return $this;
    }

    public function getProfilePictureUrl(): ?string
    {
        return $this->profile_picture_url;
    }

    public function addAce(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces): self
    {
        $this->aces[] = $aces;
        return $this;
    }

    public function removeAce(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces): void
    {
        $this->aces->removeElement($aces);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int|string,\Acts\CamdramSecurityBundle\Entity\AccessControlEntry>
     */
    public function getAces()
    {
        return $this->aces;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int|string,\Acts\CamdramSecurityBundle\Entity\AccessControlEntry>
     */
    public function getSecurityAces()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', 'security'));

        return $this->getAces()->matching($criteria);
    }

    public function addAceGrant(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants): self
    {
        $this->ace_grants[] = $aceGrants;
        return $this;
    }

    public function removeAceGrant(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants): void
    {
        $this->ace_grants->removeElement($aceGrants);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection<int|string,\Acts\CamdramSecurityBundle\Entity\AccessControlEntry>
     */
    public function getAceGrants()
    {
        return $this->ace_grants;
    }

    public function addAuthorization(\Acts\CamdramApiBundle\Entity\Authorization $authorizations): self
    {
        $this->authorizations[] = $authorizations;
        return $this;
    }

    public function removeAuthorization(\Acts\CamdramApiBundle\Entity\Authorization $authorizations): void
    {
        $this->authorizations->removeElement($authorizations);
    }

    /**
     * Get authorizations
     *
     * @return \Doctrine\Common\Collections\Collection<int|string,\Acts\CamdramApiBundle\Entity\Authorization>
     */
    public function getAuthorizations()
    {
        return $this->authorizations;
    }

}
