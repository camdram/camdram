<?php

namespace Acts\CamdramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActsUsers
 *
 * @ORM\Table(name="acts_users")
 * @ORM\Entity
 */
class ActsUsers
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


}
