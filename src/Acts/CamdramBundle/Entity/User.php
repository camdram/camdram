<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

use Acts\CamdramBundle\Entity\Person;

/**
 * User
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity(repositoryClass="Acts\CamdramBundle\Entity\UserRepository")
 */
class User implements \Serializable, UserInterface
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @Assert\Email(checkMX = true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=32, nullable=true)
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
     * @ORM\Column(name="occupation", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="graduation", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $graduation;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=50, nullable=true)
     */
    private $tel;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbemail", type="boolean", nullable=true)
     */
    private $db_email;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dbphone", type="boolean", nullable=true)
     */
    private $db_phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="forumnotify", type="boolean", nullable=true)
     */
    private $forum_notify;

    /**
     * @var boolean
     *
     * @ORM\Column(name="threadmessages", type="boolean", nullable=true)
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
     * @ORM\Column(name="resetcode", type="string", length=32, nullable=true)
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
     * @var boolean
     *
     * @ORM\Column(name="upgraded_at", type="datetime", nullable=true)
     */
    private $upgraded_at;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramSecurityBundle\Entity\ExternalUser", mappedBy="user")
     */
    private $external_users;

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

    public function getDisplayName()
    {
        return $this->getName();
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
        return $this->getEmail();
    }

    public function getSalt()
    {
        return '';
    }

    public function getRoles()
    {
        $roles = array();
        return $roles;
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
    public function setPerson(Person $person = null)
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
        $this->contact = false;
        $this->alumni = false;
        $this->publish_email = false;
        $this->forum_notify = false;
        $this->hear_about = '';
        $this->tel = '';
        $this->db_email = false;
        $this->db_phone = false;
        $this->thread_messages = false;
        $this->reverse_time = true;

        $this->registered = new \DateTime;
        $this->login = new \DateTime;
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

    /**
     * Set upgraded_at
     *
     * @param \DateTime $upgradedAt
     * @return User
     */
    public function setUpgradedAt($upgradedAt)
    {
        $this->upgraded_at = $upgradedAt;
    
        return $this;
    }

    /**
     * Get upgraded_at
     *
     * @return \DateTime 
     */
    public function getUpgradedAt()
    {
        return $this->upgraded_at;
    }

    public function __toString()
    {
        return $this->getName().' ('.$this->getEmail().')';
    }

    /**
     * Add external_users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     * @return User
     */
    public function addExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers)
    {
        $this->external_users[] = $externalUsers;
    
        return $this;
    }

    /**
     * Remove external_users
     *
     * @param \Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers
     */
    public function removeExternalUser(\Acts\CamdramSecurityBundle\Entity\ExternalUser $externalUsers)
    {
        $this->external_users->removeElement($externalUsers);
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
}