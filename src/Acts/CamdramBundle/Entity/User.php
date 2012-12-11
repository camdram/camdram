<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\Criteria;

/**
 * User
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=32, nullable=false)
     */
    private $password;

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
    private $publish_email;

    /**
     * @var string
     *
     * @ORM\Column(name="hearabout", type="text", nullable=false)
     */
    private $hear_about;

    /**
     * @var string
     *
     * @ORM\Column(name="occupation", type="string", length=255, nullable=false)
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="graduation", type="string", length=255, nullable=false)
     */
    private $graduation;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=50, nullable=false)
     */
    private $tel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbemail", type="boolean", nullable=false)
     */
    private $db_email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbphone", type="boolean", nullable=false)
     */
    private $db_phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="forumnotify", type="boolean", nullable=false)
     */
    private $forum_notify;

    /**
     * @var boolean
     *
     * @ORM\Column(name="threadmessages", type="boolean", nullable=false)
     */
    private $thread_messages;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reversetime", type="boolean", nullable=false)
     */
    private $reverse_time;

    /**
     * @var string
     *
     * @ORM\Column(name="resetcode", type="string", length=32, nullable=false)
     */
    private $reset_code;

    /**
     * @var integer
     *
     * @ORM\Column(name="person_id", type="integer", nullable=true)
     */
    private $person_id;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="users")
     *
     */
    private $person;

    /**
     * @var \User
     *
     *  @ORM\OneToMany(targetEntity="UserIdentity", mappedBy="user")
     */
    private $identities;

    /**
     * @var boolean
     *
     * @ORM\Column(name="upgraded", type="boolean", nullable=false)
     */
    private $upgraded;


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

    /**
     * Set email
     *
     * @param string $email
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
     * @return User
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
     * @return User
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
     * @return User
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
     * @return User
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
     * Set publish_email
     *
     * @param boolean $publishEmail
     * @return User
     */
    public function setPublishEmail($publishEmail)
    {
        $this->publish_email = $publishEmail;
    
        return $this;
    }

    /**
     * Get publish_email
     *
     * @return boolean 
     */
    public function getPublishEmail()
    {
        return $this->publish_email;
    }

    /**
     * Set forum_notify
     *
     * @param boolean $forumNotify
     * @return User
     */
    public function setForumNotify($forumNotify)
    {
        $this->forum_notify = $forumNotify;
    
        return $this;
    }

    /**
     * Get forum_notify
     *
     * @return boolean 
     */
    public function getForumNotify()
    {
        return $this->forum_notify;
    }

    /**
     * Set hear_about
     *
     * @param string $hearAbout
     * @return User
     */
    public function setHearAbout($hearAbout)
    {
        $this->hear_about = $hearAbout;
    
        return $this;
    }

    /**
     * Get hear_about
     *
     * @return string 
     */
    public function getHearAbout()
    {
        return $this->hear_about;
    }

    /**
     * Set occupation
     *
     * @param string $occupation
     * @return User
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
     * @return User
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
     * @return User
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
     * Set db_email
     *
     * @param boolean $dbEmail
     * @return User
     */
    public function setDbEmail($dbEmail)
    {
        $this->db_email = $dbEmail;
    
        return $this;
    }

    /**
     * Get db_email
     *
     * @return boolean 
     */
    public function getDbEmail()
    {
        return $this->db_email;
    }

    /**
     * Set db_phone
     *
     * @param boolean $dbPhone
     * @return User
     */
    public function setDbPhone($dbPhone)
    {
        $this->db_phone = $dbPhone;
    
        return $this;
    }

    /**
     * Get db_phone
     *
     * @return boolean 
     */
    public function getDbPhone()
    {
        return $this->db_phone;
    }

    /**
     * Set thread_messages
     *
     * @param boolean $threadMessages
     * @return User
     */
    public function setThreadMessages($threadMessages)
    {
        $this->thread_messages = $threadMessages;
    
        return $this;
    }

    /**
     * Get thread_messages
     *
     * @return boolean 
     */
    public function getThreadMessages()
    {
        return $this->thread_messages;
    }

    /**
     * Set reverse_time
     *
     * @param boolean $reverseTime
     * @return User
     */
    public function setReverseTime($reverseTime)
    {
        $this->reverse_time = $reverseTime;
    
        return $this;
    }

    /**
     * Get reverse_time
     *
     * @return boolean 
     */
    public function getReverseTime()
    {
        return $this->reverse_time;
    }

    /**
     * Set reset_code
     *
     * @param string $resetCode
     * @return User
     */
    public function setResetCode($resetCode)
    {
        $this->reset_code = $resetCode;
    
        return $this;
    }

    /**
     * Get reset_code
     *
     * @return string 
     */
    public function getResetCode()
    {
        return $this->reset_code;
    }

    /**
     * Set password
     *
     * @param string $password
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
        return $this->getId();
    }

    public function getSalt()
    {
        return '';
    }
    public function getRoles()
    {
        return array();
    }
    public function eraseCredentials()
    {

    }

    /**
     * Set person_id
     *
     * @param integer $personId
     * @return User
     */
    public function setPersonId($personId)
    {
        $this->person_id = $personId;
    
        return $this;
    }

    /**
     * Get person_id
     *
     * @return integer 
     */
    public function getPersonId()
    {
        return $this->person_id;
    }

    /**
     * Set person
     *
     * @param \Acts\CamdramBundle\Entity\Person $person
     * @return User
     */
    public function setPerson(\Acts\CamdramBundle\Entity\Person $person = null)
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
        $this->identities = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add identities
     *
     * @param \Acts\CamdramBundle\Entity\UserIdentity $identities
     * @return User
     */
    public function addIdentity(\Acts\CamdramBundle\Entity\UserIdentity $identities)
    {
        $this->identities[] = $identities;
    
        return $this;
    }

    /**
     * Remove identities
     *
     * @param \Acts\CamdramBundle\Entity\UserIdentity $identities
     */
    public function removeIdentity(\Acts\CamdramBundle\Entity\UserIdentity $identities)
    {
        $this->identities->removeElement($identities);
    }

    /**
     * Get identities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    public function getIdentityByServiceName($service_name)
    {
        if (!is_string($service_name)) {
            throw new \InvalidArgumentException('The service name given to User::getIdentityByServiceName() must be a string');
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq("service", $service_name))
        ;
        $res = $this->getIdentities()->matching($criteria);
        if (count($res) > 0) {
            return $res[0];
        }
        else return false;
    }


    //Two stub methods because Doctrine can't deal with the irregular singularisation of 'identities'...
    public function addIdentitie(\Acts\CamdramBundle\Entity\UserIdentity $identities)
    {
    }
    public function removeIdentitie(\Acts\CamdramBundle\Entity\UserIdentity $identities)
    {
    }

    public function serialize()
    {
        return serialize(array(
                $this->id, $this->name, $this->email, $this->password, $this->registered,
                $this->login, $this->person_id
        ));
    }
    public function unserialize($serialized)
    {
        list( $this->id, $this->name, $this->email, $this->password, $this->registered,
            $this->login, $this->person_id) = unserialize($serialized);
    }

    /**
     * Set upgraded
     *
     * @param boolean $upgraded
     * @return User
     */
    public function setUpgraded($upgraded)
    {
        $this->upgraded = $upgraded;
    
        return $this;
    }

    /**
     * Get upgraded
     *
     * @return boolean 
     */
    public function getUpgraded()
    {
        return $this->upgraded;
    }
}