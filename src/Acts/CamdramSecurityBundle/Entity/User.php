<?php

namespace Acts\CamdramSecurityBundle\Entity;

use Acts\CamdramSecurityBundle\Security\User\CamdramUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use Acts\CamdramBundle\Entity\Person;

/**
 * User
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\EntityListeners({"Acts\CamdramSecurityBundle\EventListener\UserListener" })
 * @UniqueEntity(fields="email", message="An account already exists with that email address")
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("user")
 */
class User implements \Serializable, CamdramUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     * @Serializer\Expose
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
     * @Assert\Length(min=8, max=100, minMessage="The password must be at least 8 characters long")
     */
    private $password;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered", type="date", nullable=false)
     */
    private $registered_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="login", type="date", nullable=false)
     */
    private $last_login_at;

    /**
     * @var bool
     *
     * @ORM\Column(name="contact", type="boolean", nullable=false)
     */
    private $contact;

    /**
     * @var bool
     *
     * @ORM\Column(name="alumni", type="boolean", nullable=false)
     */
    private $alumni;

    /**
     * @var bool
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
     * @Serializer\Expose
     */
    private $occupation;

    /**
     * @var string
     *
     * @ORM\Column(name="graduation", type="string", length=255, nullable=true)
     * @Serializer\Expose
     */
    private $graduation;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=50, nullable=true)
     * @Serializer\Exclude()
     */
    private $tel;

    /**
     * @var bool
     *
     * @ORM\Column(name="dbemail", type="boolean", nullable=true)
     */
    private $db_email;

    /**
     * @var bool
     *
     * @ORM\Column(name="dbphone", type="boolean", nullable=true)
     */
    private $db_phone;

    /**
     * @var bool
     *
     * @ORM\Column(name="forumnotify", type="boolean", nullable=true)
     */
    private $forum_notify;

    /**
     * @var bool
     *
     * @ORM\Column(name="threadmessages", type="boolean", nullable=true)
     */
    private $thread_messages;

    /**
     * @var bool
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
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity="\Acts\CamdramBundle\Entity\Person", inversedBy="users")
     */
    private $person;

    /**
     * @var bool
     *
     * @ORM\Column(name="upgraded_at", type="datetime", nullable=true)
     */
    private $upgraded_at;

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
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramLegacyBundle\Entity\KnowledgeBaseRevision", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $knowledge_base_revisions;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="AccessControlEntry", mappedBy="grantedBy")
     * @Serializer\Exclude()
     */
    private $ace_grants;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramLegacyBundle\Entity\Email", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $email_builders;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramLegacyBundle\Entity\EmailAlias", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $email_aliases;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramLegacyBundle\Entity\EmailSig", mappedBy="user")
     * @Serializer\Exclude()
     */
    private $email_sigs;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Acts\CamdramAdminBundle\Entity\Support", mappedBy="owner")
     * @Serializer\Exclude()
     */
    private $owned_issues;

    /**
     * @ORM\ManyToMany(targetEntity="Acts\CamdramApiBundle\Entity\ExternalApp", mappedBy="users")
     * @Serializer\Exclude()
     */
    private $apps;

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
        /* Store emails in a Camdram v1 compatible way, i.e. strip cam.ac.uk
         * and hermes.cam.ac.uk suffixes. This modification is identical to the
         * EmailtoUser function in v1 as a result.
         */
        /*$email=preg_replace("/@cam.ac.uk$/i","",$email);
        $email=preg_replace("/@hermes.cam.ac.uk$/i","",$email);*/
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
        /*if ($email && strchr($email,'@') === false) {
            $email .= "@cam.ac.uk";
        }*/
        return $email;
    }

    public function getFullEmail()
    {
        /* Add missing suffix, if required. */
        $email = $this->email;
        if ($email && strchr($email, '@') === false) {
            $email .= '@cam.ac.uk';
        }

        return $email;
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
     * Set contact
     *
     * @param bool $contact
     *
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
     * @return bool
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set alumni
     *
     * @param bool $alumni
     *
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
     * @return bool
     */
    public function getAlumni()
    {
        return $this->alumni;
    }

    /**
     * Set publish_email
     *
     * @param bool $publishEmail
     *
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
     * @return bool
     */
    public function getPublishEmail()
    {
        return $this->publish_email;
    }

    /**
     * Set forum_notify
     *
     * @param bool $forumNotify
     *
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
     * @return bool
     */
    public function getForumNotify()
    {
        return $this->forum_notify;
    }

    /**
     * Set hear_about
     *
     * @param string $hearAbout
     *
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
     *
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
     *
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
     *
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
     * @param bool $dbEmail
     *
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
     * @return bool
     */
    public function getDbEmail()
    {
        return $this->db_email;
    }

    /**
     * Set db_phone
     *
     * @param bool $dbPhone
     *
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
     * @return bool
     */
    public function getDbPhone()
    {
        return $this->db_phone;
    }

    /**
     * Set thread_messages
     *
     * @param bool $threadMessages
     *
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
     * @return bool
     */
    public function getThreadMessages()
    {
        return $this->thread_messages;
    }

    /**
     * Set reverse_time
     *
     * @param bool $reverseTime
     *
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
     * @return bool
     */
    public function getReverseTime()
    {
        return $this->reverse_time;
    }

    /**
     * Set reset_code
     *
     * @param string $resetCode
     *
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

        $this->registered_at = new \DateTime();
        $this->last_login_at = new \DateTime();

        $this->aces = new ArrayCollection();
        $this->external_users = new ArrayCollection();
    }

    public function serialize()
    {
        return serialize(array(
                $this->id, $this->name, $this->email, $this->password, $this->registered_at,
                $this->last_login_at, $this->occupation, $this->graduation, $this->is_email_verified
        ));
    }
    public function unserialize($serialized)
    {
        list($this->id, $this->name, $this->email, $this->password, $this->registered_at,
            $this->last_login_at, $this->occupation, $this->graduation,
            $this->is_email_verified) = unserialize($serialized);
    }

    /**
     * Set upgraded
     *
     * @param bool $upgraded
     *
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
     * @return bool
     */
    public function getUpgraded()
    {
        return $this->upgraded;
    }

    /**
     * Set upgraded_at
     *
     * @param \DateTime $upgradedAt
     *
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
        $criteria->where(Criteria::expr()->eq('type', 'security'))
            ->andWhere(Criteria::expr()->isNull('revokedBy'))
            ->andWhere(Criteria::expr()->neq('grantedBy', null));

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
     * Add knowledge_base_revisions
     *
     * @param \Acts\CamdramLegacyBundle\Entity\KnowledgeBaseRevision $knowledgeBaseRevisions
     *
     * @return User
     */
    public function addKnowledgeBaseRevision(\Acts\CamdramLegacyBundle\Entity\KnowledgeBaseRevision $knowledgeBaseRevisions)
    {
        $this->knowledge_base_revisions[] = $knowledgeBaseRevisions;

        return $this;
    }

    /**
     * Remove knowledge_base_revisions
     *
     * @param \Acts\CamdramLegacyBundle\Entity\KnowledgeBaseRevision $knowledgeBaseRevisions
     */
    public function removeKnowledgeBaseRevision(\Acts\CamdramLegacyBundle\Entity\KnowledgeBaseRevision $knowledgeBaseRevisions)
    {
        $this->knowledge_base_revisions->removeElement($knowledgeBaseRevisions);
    }

    /**
     * Get knowledge_base_revisions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getKnowledgeBaseRevisions()
    {
        return $this->knowledge_base_revisions;
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
     * Add email_builders
     *
     * @param \Acts\CamdramLegacyBundle\Entity\Email $emailBuilders
     *
     * @return User
     */
    public function addEmailBuilder(\Acts\CamdramLegacyBundle\Entity\Email $emailBuilders)
    {
        $this->email_builders[] = $emailBuilders;

        return $this;
    }

    /**
     * Remove email_builders
     *
     * @param \Acts\CamdramLegacyBundle\Entity\Email $emailBuilders
     */
    public function removeEmailBuilder(\Acts\CamdramLegacyBundle\Entity\Email $emailBuilders)
    {
        $this->email_builders->removeElement($emailBuilders);
    }

    /**
     * Get email_builders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmailBuilders()
    {
        return $this->email_builders;
    }

    /**
     * Add owned_issues
     *
     * @param \Acts\CamdramAdminBundle\Entity\Support $ownedIssues
     *
     * @return User
     */
    public function addOwnedIssue(\Acts\CamdramAdminBundle\Entity\Support $ownedIssues)
    {
        $this->owned_issues[] = $ownedIssues;

        return $this;
    }

    /**
     * Remove owned_issues
     *
     * @param \Acts\CamdramAdminBundle\Entity\Support $ownedIssues
     */
    public function removeOwnedIssue(\Acts\CamdramAdminBundle\Entity\Support $ownedIssues)
    {
        $this->owned_issues->removeElement($ownedIssues);
    }

    /**
     * Get owned_issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwnedIssues()
    {
        return $this->owned_issues;
    }

    /**
     * Add email_aliases
     *
     * @param \Acts\CamdramLegacyBundle\Entity\EmailAlias $emailAliases
     *
     * @return User
     */
    public function addEmailAlias(\Acts\CamdramLegacyBundle\Entity\EmailAlias $emailAliases)
    {
        $this->email_aliases[] = $emailAliases;

        return $this;
    }

    /**
     * Remove email_aliases
     *
     * @param \Acts\CamdramLegacyBundle\Entity\EmailAlias $emailAliases
     */
    public function removeEmailAlias(\Acts\CamdramLegacyBundle\Entity\EmailAlias $emailAliases)
    {
        $this->email_aliases->removeElement($emailAliases);
    }

    /**
     * Get email_aliases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmailAliases()
    {
        return $this->email_aliases;
    }

    /**
     * Add email_sigs
     *
     * @param \Acts\CamdramLegacyBundle\Entity\EmailSig $emailSigs
     *
     * @return User
     */
    public function addEmailSig(\Acts\CamdramLegacyBundle\Entity\EmailSig $emailSigs)
    {
        $this->email_sigs[] = $emailSigs;

        return $this;
    }

    /**
     * Remove email_sigs
     *
     * @param \Acts\CamdramLegacyBundle\Entity\EmailSig $emailSigs
     */
    public function removeEmailSig(\Acts\CamdramLegacyBundle\Entity\EmailSig $emailSigs)
    {
        $this->email_sigs->removeElement($emailSigs);
    }

    /**
     * Get email_sigs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmailSigs()
    {
        return $this->email_sigs;
    }
}
