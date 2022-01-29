<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * External User
 *
 * @ORM\Table(name="acts_external_users")
 * @ORM\Entity
 */
class ExternalUser implements \Serializable
{
    public function __construct()
    {
        $this->last_login_at = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="external_users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="service", type="string", length=50, nullable=false)
     */
    private $service;

    /**
     * @var ?string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=true)
     */
    private $username;

    /**
     * @var ?string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var ?string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var ?string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var ?string
     *
     * @ORM\Column(name="profile_picture_url", type="string", length=8192, nullable=true)
     */
    private $profile_picture_url;

    /**
     * @var ?\DateTime
     *
     * @ORM\Column(name="last_login_at", type="datetime", nullable=true)
     */
    private $last_login_at;

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
     * Set service
     *
     * @param string $service
     *
     * @return ExternalUser
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
     * Set username
     *
     * @param string $username
     *
     * @return ExternalUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return ExternalUser
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ExternalUser
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
     * Set name
     *
     * @param string $name
     *
     * @return ExternalUser
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
        if ($this->getUser()) {
            return $this->getUser()->getName();
        } elseif ($this->getName()) {
            return $this->getName();
        } else {
            return $this->getUsername();
        }
    }

    public function setUser(User $user = null): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ?User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getType()
    {
        return 'external';
    }

    /**
     * Set profile_picture_url
     *
     * @param string $profilePictureUrl
     *
     * @return ExternalUser
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

    public function __toString()
    {
        return $this->getId() . ':' . $this->getName();
    }

    public function serialize()
    {
        return serialize(array(
            $this->id, $this->name, $this->email, $this->service,
            $this->username
        ));
    }
    public function unserialize($serialized)
    {
        list(
            $this->id, $this->name, $this->email, $this->service,
            $this->username
        ) = unserialize($serialized);
    }

    /**
     * Set last_login_at
     *
     * @param \DateTime $lastLoginAt
     *
     * @return ExternalUser
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->last_login_at = $lastLoginAt;

        return $this;
    }

    /**
     * Get last_login_at
     *
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->last_login_at;
    }
}
