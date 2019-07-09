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
     * @Assert\Email(checkMX = true)
     * @Serializer\Groups({"user_email"})
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=32, nullable=true)
     * @Assert\Length(min=8, max=100, minMessage="The password must be at least 8 characters long")
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime", nullable=true)
     */
    private $registered_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    private $last_login_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_password_login_at", type="datetime", nullable=true)
     */
    private $last_password_login_at;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\Person", inversedBy="users")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $person;

    /**
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
     * @var string
     *
     * @ORM\Column(name="profile_picture_url", type="string", nullable=true)
     */
    private $profile_picture_url;

    /**
     * @var array
     *
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
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AccessControlEntry", mappedBy="grantedBy")
     * @Serializer\Exclude()
     *
     */
    private $ace_grants;

    /**
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
     * @param \DateTime $registered
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

    /**
     * Set login
     *
     * @param \DateTime $login
     *
     * @return User
     */
    public function setLastLoginAt($last_login_at)
    {
        $this->last_login_at = $last_login_at;

        return $this;
    }

    /**
     * Get login
     *
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->last_login_at;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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

    /**
     * Set person
     *
     * @param \Acts\CamdramBundle\Entity\Person $person
     *
     * @return User
     */
    public function setPerson(Person $person = null)
    {
        $this->person = $person;
        foreach ($this->getExternalUsers() as $external_user) {
            $external_user->setPerson($person);
        }

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

        $this->aces = new ArrayCollection();
        $this->external_users = new ArrayCollection();
    }

    public function serialize()
    {
        return serialize(array(
                $this->id, $this->name, $this->email, $this->password
        ));
    }
    public function unserialize($serialized)
    {
        list($this->id, $this->name, $this->email, $this->password) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getName().' ('.$this->getEmail().')';
    }

    /**
     * Add external_users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     *
     * @return User
     */
    public function addExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUser)
    {
        $this->external_users[] = $externalUser;

        if (!$this->getProfilePictureUrl()) {
            $this->setProfilePictureUrl($externalUser->getProfilePictureUrl());
        }

        return $this;
    }

    /**
     * Remove external_users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     */
    public function removeExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUser)
    {
        $this->external_users->removeElement($externalUser);
    }

    /**
     * Get external_users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExternalUsers()
    {
        return $this->external_users;
    }

    public function getExternalUserByService($service)
    {
        if (is_null($this->external_users)) {
            return null;
        }

        $criteria = Criteria::create()
        ->where(Criteria::expr()->eq('service', $service));
        $res = $this->external_users->matching($criteria);
        if (count($res) > 0) {
            return $res->first();
        }
    }

    public function getType()
    {
        return 'native';
    }

    /**
     * Set is_email_verified
     *
     * @param bool $isEmailVerified
     *
     * @return User
     */
    public function setIsEmailVerified($isEmailVerified)
    {
        $this->is_email_verified = $isEmailVerified;

        return $this;
    }

    /**
     * Get is_email_verified
     *
     * @return bool
     */
    public function getIsEmailVerified()
    {
        return $this->is_email_verified;
    }

    /**
     * Set profile_picture_url
     *
     * @param string $profilePictureUrl
     *
     * @return User
     */
    public function setProfilePictureUrl($profilePictureUrl)
    {
        $this->profile_picture_url = $profilePictureUrl;

        return $this;
    }

    /**
     * Get profile_picture_url
     *
     * @return string
     */
    public function getProfilePictureUrl()
    {
        return $this->profile_picture_url;
    }

    /**
     * Add aces
     *
     * @param \Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces
     *
     * @return User
     */
    public function addAce(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces)
    {
        $this->aces[] = $aces;

        return $this;
    }

    /**
     * Remove aces
     *
     * @param \Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces
     */
    public function removeAce(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aces)
    {
        $this->aces->removeElement($aces);
    }

    /**
     * Get aces
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAces()
    {
        return $this->aces;
    }

    public function getSecurityAces()
    {
        $criteria = Criteria::create();
        $criteria->where(Criteria::expr()->eq('type', 'security'));

        return $this->getAces()->matching($criteria);
    }

    /**
     * Add apps
     *
     * @param \Acts\CamdramApiBundle\Entity\ExternalApp $apps
     *
     * @return User
     */
    public function addApp(\Acts\CamdramApiBundle\Entity\ExternalApp $apps)
    {
        $this->apps[] = $apps;

        return $this;
    }

    /**
     * Remove apps
     *
     * @param \Acts\CamdramApiBundle\Entity\ExternalApp $apps
     */
    public function removeApp(\Acts\CamdramApiBundle\Entity\ExternalApp $apps)
    {
        $this->apps->removeElement($apps);
    }

    /**
     * Get apps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getApps()
    {
        return $this->apps;
    }

    /**
     * Add ace_grants
     *
     * @param \Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants
     *
     * @return User
     */
    public function addAceGrant(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants)
    {
        $this->ace_grants[] = $aceGrants;

        return $this;
    }

    /**
     * Remove ace_grants
     *
     * @param \Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants
     */
    public function removeAceGrant(\Acts\CamdramSecurityBundle\Entity\AccessControlEntry $aceGrants)
    {
        $this->ace_grants->removeElement($aceGrants);
    }

    /**
     * Get ace_grants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAceGrants()
    {
        return $this->ace_grants;
    }

    /**
     * Add authorizations
     *
     * @param \Acts\CamdramApiBundle\Entity\Authorization $authorizations
     * @return User
     */
    public function addAuthorization(\Acts\CamdramApiBundle\Entity\Authorization $authorizations)
    {
        $this->authorizations[] = $authorizations;
        return $this;
    }

    /**
     * Remove authorizations
     *
     * @param \Acts\CamdramApiBundle\Entity\Authorization $authorizations
     */
    public function removeAuthorization(\Acts\CamdramApiBundle\Entity\Authorization $authorizations)
    {
        $this->authorizations->removeElement($authorizations);
    }

    /**
     * Get authorizations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAuthorizations()
    {
        return $this->authorizations;
    }

    /**
     * Set lastPasswordLoginAt
     *
     * @param \DateTime $lastPasswordLoginAt
     *
     * @return User
     */
    public function setLastPasswordLoginAt($lastPasswordLoginAt)
    {
        $this->last_password_login_at = $lastPasswordLoginAt;

        return $this;
    }

    /**
     * Get lastPasswordLoginAt
     *
     * @return \DateTime
     */
    public function getLastPasswordLoginAt()
    {
        return $this->last_password_login_at;
    }

}
