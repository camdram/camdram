<?php
namespace Acts\CamdramSecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
* User
*
* @ORM\Table(name="acts_user_identities")
* @ORM\Entity
*/
class UserIdentity
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
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $user_id;

    /**
     * @var \User
     *
     *  @ORM\ManyToOne(targetEntity="Acts\CamdramBundle\Entity\User", inversedBy="identities")
     *  @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
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
    * @ORM\Column(name="remote_user", type="string", length=255, nullable=true)
    */
    private $remote_user;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="token_secret", type="string", length=255, nullable=true)
     */
    private $token_secret;

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
     * Set user_id
     *
     * @param integer $userId
     * @return UserIdentity
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
     * Set service
     *
     * @param string $service
     * @return UserIdentity
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
     * @return UserIdentity
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
     * Set remote_user
     *
     * @param string $remoteUser
     * @return UserIdentity
     */
    public function setRemoteUser($remoteUser)
    {
        $this->remote_user = $remoteUser;
    
        return $this;
    }

    /**
     * Get remote_user
     *
     * @return string 
     */
    public function getRemoteUser()
    {
        return $this->remote_user;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramBundle\Entity\User $user
     * @return UserIdentity
     */
    public function setUser(\Acts\CamdramBundle\Entity\User $user = null)
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
     * Set token
     *
     * @param string $token
     * @return UserIdentity
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
     * Set token_secret
     *
     * @param string $tokenSecret
     * @return UserIdentity
     */
    public function setTokenSecret($tokenSecret)
    {
        $this->token_secret = $tokenSecret;
    
        return $this;
    }

    /**
     * Get token_secret
     *
     * @return string 
     */
    public function getTokenSecret()
    {
        return $this->token_secret;
    }

    public function loadAccessToken($access_token)
    {
        if (is_array($access_token)) {
            if (isset($access_token['token'])) $this->setToken($access_token['token']);
            if (isset($access_token['token_secret']))$this->setTokenSecret($access_token['token_secret']);
        }
        else $this->setToken($access_token);
    }
}
