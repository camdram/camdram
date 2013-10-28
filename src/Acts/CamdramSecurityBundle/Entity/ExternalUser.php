<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Acts\CamdramBundle\Entity\User;

/**
* External User
*
* @ORM\Table(name="acts_external_users")
* @ORM\Entity
*/
class ExternalUser implements CamdramUserInterface
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\User", inversedBy="external_users")
     */
    private $user;

    /**
    * @var string
    *
    * @ORM\Column(name="service", type="string", length=50, nullable=false)
    */
    private $service;

    /**
    * @var integer
    *
    * @ORM\Column(name="remote_id", type="string", length=100, nullable=true)
    */
    private $remote_id;

    /**
    * @var string
    *
    * @ORM\Column(name="username", type="string", length=255, nullable=true)
    */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_picture_url", type="string", length=255, nullable=true)
     */
    private $profile_picture_url;

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
     * Set service
     *
     * @param string $service
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
     * Set remote_id
     *
     * @param string $remoteId
     * @return ExternalUser
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
     * Set username
     *
     * @param string $username
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
     * @inheritdoc
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getRoles() {
        $roles = array('ROLE_USER');
        if ($this->getUser()) {
            $roles = array_merge($roles, $this->getUser()->getRoles());
        }
        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }


    /**
     * Set name
     *
     * @param string $name
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
        }
        elseif ($this->getName()) {
            return $this->getName();
        }
        else {
            return $this->getUsername();
        }
    }


    /**
     * Set linked_user
     *
     * @param \Acts\CamdramBundle\Entity\User $linkedUser
     * @return ExternalUser
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get linked_user
     *
     * @return \Acts\CamdramBundle\Entity\User 
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
}