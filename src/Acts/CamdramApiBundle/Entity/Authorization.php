<?php
namespace Acts\CamdramApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\AuthCode as BaseAuthCode;
use Doctrine\ORM\Mapping as ORM;
use FOS\OAuthServerBundle\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * API Access Tokens
 *
 * @ORM\Table(name="acts_api_authorizations")
 * @ORM\Entity(repositoryClass="AuthorizationRepository")
 */
class Authorization
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="ExternalApp")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="Acts\CamdramSecurityBundle\Entity\User", inversedBy="authorizations")
     */
    protected $user;

    /**
     * @ORM\Column(type="simple_array")
     */
    protected $scopes;

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
     * Set client
     *
     * @param \Acts\CamdramApiBundle\Entity\ApiApp $client
     * @return AccessToken
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Acts\CamdramApiBundle\Entity\ApiApp
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set user
     *
     * @param \Acts\CamdramSecurityBundle\Entity\User $user
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

    /**
     * Set scopes
     *
     * @param array $scopes
     * @return Authorization
     */
    public function setScopes($scopes)
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * Get scopes
     *
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }

    /**
     * @param $scope
     * @return bool
     */
    public function hasScope($scope)
    {
        return in_array($scope, $this->scopes);
    }

    /**
     * @param array $scopes
     * @return bool
     */
    public function hasScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            if (!in_array($scope, $this->scopes)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $scope
     */
    public function addScope($scope)
    {
        if (!in_array($scope, $this->scopes)) {
            $this->scopes[] = $scope;
        }

        return $this;
    }

    /**
     * @param array $scopes
     * @return $this
     */
    public function addScopes(array $scopes)
    {
        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }

        return $this;
    }
}
