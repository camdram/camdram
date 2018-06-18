<?php

namespace Acts\CamdramBackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailBounce
 *
 * @ORM\Table(name="acts_email_bounces")
 * @ORM\Entity
 */
class EmailBounce
{
    public function __construct()
    {
        $this->receivedAt = new \DateTime();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="from_header", type="string", length=255)
     */
    private $fromHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="to_header", type="string", length=255)
     */
    private $toHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received_at", type="datetime")
     */
    private $receivedAt;

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
     * Set fromHeader
     *
     * @param string $fromHeader
     *
     * @return EmailBounce
     */
    public function setFromHeader($fromHeader)
    {
        $this->fromHeader = $fromHeader;

        return $this;
    }

    /**
     * Get fromHeader
     *
     * @return string
     */
    public function getFromHeader()
    {
        return $this->fromHeader;
    }

    /**
     * Set toHeader
     *
     * @param string $toHeader
     *
     * @return EmailBounce
     */
    public function setToHeader($toHeader)
    {
        $this->toHeader = $toHeader;

        return $this;
    }

    /**
     * Get toHeader
     *
     * @return string
     */
    public function getToHeader()
    {
        return $this->toHeader;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailBounce
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return EmailBounce
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set receivedAt
     *
     * @param \DateTime $receivedAt
     *
     * @return EmailBounce
     */
    public function setReceivedAt($receivedAt)
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    /**
     * Get receivedAt
     *
     * @return \DateTime
     */
    public function getReceivedAt()
    {
        return $this->receivedAt;
    }
}
