<?php

namespace Acts\CamdramApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="acts_rate_limit_entries")
 * @ORM\Entity(repositoryClass="RateLimitEntryRepository")
 */
class RateLimitEntry
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ip_address;

    /**
     * @ORM\Column(type="string")
     */
    protected $endpoint;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $occurred_at;

    public function getId()
    {
        return $this->id;
    }

    public function getIpAddress()
    {
        return $this->id;
    }

    public function setIpAddress($ip_address)
    {
        $int = ip2long($ip_address);
        $this->ip_address = $int;
    }

    public function getEndpoint()
    {
        return $this->endpoint;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }

    public function getOccurredAt()
    {
        return $this->occurred_at;
    }

    public function setOccurredAt($occurred_at)
    {
        $this->occurred_at = $occurred_at;
    }
}
