<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsUsers
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity
 */
class User
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
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="text", nullable=false)
     */
    private $pass;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="date", nullable=false)
     */
    private $registered;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login", type="date", nullable=false)
     */
    private $login;

    /**
     * @var boolean
     *
     * @ORM\Column(name="contact", type="boolean", nullable=false)
     */
    private $contact;

    /**
     * @var boolean
     *
     * @ORM\Column(name="alumni", type="boolean", nullable=false)
     */
    private $alumni;

    /**
     * @var boolean
     *
     * @ORM\Column(name="publishemail", type="boolean", nullable=false)
     */
    private $publishemail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="forumnotify", type="boolean", nullable=false)
     */
    private $forumnotify;

    /**
     * @var string
     *
     * @ORM\Column(name="hearabout", type="text", nullable=false)
     */
    private $hearabout;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="text", nullable=false)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="graduation", type="text", nullable=false)
     */
    private $graduation;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="text", nullable=false)
     */
    private $tel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbemail", type="boolean", nullable=false)
     */
    private $dbemail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbphone", type="boolean", nullable=false)
     */
    private $dbphone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="threadmessages", type="boolean", nullable=false)
     */
    private $threadmessages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversetime", type="boolean", nullable=false)
     */
    private $reversetime;

    /**
     * @var string
     *
     * @ORM\Column(name="resetcode", type="text", nullable=false)
     */
    private $resetcode;



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
     * Set name
     *
     * @param string $name
     * @return ActsUsers
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

    /**
     * Set email
     *
     * @param string $email
     * @return ActsUsers
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
     * Set pass
     *
     * @param string $pass
     * @return ActsUsers
     */
    public function setPass($pass)
    {
        $this->pass = $pass;
    
        return $this;
    }

    /**
     * Get pass
     *
     * @return string 
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * Set registered
     *
     * @param \DateTime $registered
     * @return ActsUsers
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
    
        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime 
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * Set login
     *
     * @param \DateTime $login
     * @return ActsUsers
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return \DateTime 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set contact
     *
     * @param boolean $contact
     * @return ActsUsers
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

    /**
     * Set alumni
     *
     * @param boolean $alumni
     * @return ActsUsers
     */
    public function setAlumni($alumni)
    {
        $this->alumni = $alumni;
    
        return $this;
    }

    /**
     * Get alumni
     *
     * @return boolean 
     */
    public function getAlumni()
    {
        return $this->alumni;
    }

    /**
     * Set publishemail
     *
     * @param boolean $publishemail
     * @return ActsUsers
     */
    public function setPublishemail($publishemail)
    {
        $this->publishemail = $publishemail;
    
        return $this;
    }

    /**
     * Get publishemail
     *
     * @return boolean 
     */
    public function getPublishemail()
    {
        return $this->publishemail;
    }

    /**
     * Set forumnotify
     *
     * @param boolean $forumnotify
     * @return ActsUsers
     */
    public function setForumnotify($forumnotify)
    {
        $this->forumnotify = $forumnotify;
    
        return $this;
    }

    /**
     * Get forumnotify
     *
     * @return boolean 
     */
    public function getForumnotify()
    {
        return $this->forumnotify;
    }

    /**
     * Set hearabout
     *
     * @param string $hearabout
     * @return ActsUsers
     */
    public function setHearabout($hearabout)
    {
        $this->hearabout = $hearabout;
    
        return $this;
    }

    /**
     * Get hearabout
     *
     * @return string 
     */
    public function getHearabout()
    {
        return $this->hearabout;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return ActsUsers
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
    
        return $this;
    }

    /**
     * Get occupation
     *
     * @return string 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set graduation
     *
     * @param string $graduation
     * @return ActsUsers
     */
    public function setGraduation($graduation)
    {
        $this->graduation = $graduation;
    
        return $this;
    }

    /**
     * Get graduation
     *
     * @return string 
     */
    public function getGraduation()
    {
        return $this->graduation;
    }

    /**
     * Set tel
     *
     * @param string $tel
     * @return ActsUsers
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    
        return $this;
    }

    /**
     * Get tel
     *
     * @return string 
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * Set dbemail
     *
     * @param boolean $dbemail
     * @return ActsUsers
     */
    public function setDbemail($dbemail)
    {
        $this->dbemail = $dbemail;
    
        return $this;
    }

    /**
     * Get dbemail
     *
     * @return boolean 
     */
    public function getDbemail()
    {
        return $this->dbemail;
    }

    /**
     * Set dbphone
     *
     * @param boolean $dbphone
     * @return ActsUsers
     */
    public function setDbphone($dbphone)
    {
        $this->dbphone = $dbphone;
    
        return $this;
    }

    /**
     * Get dbphone
     *
     * @return boolean 
     */
    public function getDbphone()
    {
        return $this->dbphone;
    }

    /**
     * Set threadmessages
     *
     * @param boolean $threadmessages
     * @return ActsUsers
     */
    public function setThreadmessages($threadmessages)
    {
        $this->threadmessages = $threadmessages;
    
        return $this;
    }

    /**
     * Get threadmessages
     *
     * @return boolean 
     */
    public function getThreadmessages()
    {
        return $this->threadmessages;
    }

    /**
     * Set reversetime
     *
     * @param boolean $reversetime
     * @return ActsUsers
     */
    public function setReversetime($reversetime)
    {
        $this->reversetime = $reversetime;
    
        return $this;
    }

    /**
     * Get reversetime
     *
     * @return boolean 
     */
    public function getReversetime()
    {
        return $this->reversetime;
    }

    /**
     * Set resetcode
     *
     * @param string $resetcode
     * @return ActsUsers
     */
    public function setResetcode($resetcode)
    {
        $this->resetcode = $resetcode;
    
        return $this;
    }

    /**
     * Get resetcode
     *
     * @return string 
     */
    public function getResetcode()
    {
        return $this->resetcode;
    }
}