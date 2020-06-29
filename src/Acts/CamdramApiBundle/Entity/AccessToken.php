<?php

namespace Acts\CamdramApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\AccessToken as BaseAccessToken;
use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * API Access Tokens
 *
 * @ORM\Table(name="acts_api_access_tokens", options={"collate"="utf8_unicode_ci", "charset"="utf8"})
 * @ORM\Entity(repositoryClass="AuthorizationRepository")
 */
class AccessToken extends BaseAccessToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var ExternalApp
     * @ORM\ManyToOne(targetEntity="ExternalApp")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    protected $client;

    /**
     * @var \Acts\CamdramSecurityBundle\Entity\User
     * @ORM\ManyToOne(targetEntity="Acts\CamdramSecurityBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setClient(ClientInterface $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getClient(): ?ClientInterface
    {
        return $this->client;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $user
     *
     * @return AccessToken
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Acts\CamdramSecurityBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
