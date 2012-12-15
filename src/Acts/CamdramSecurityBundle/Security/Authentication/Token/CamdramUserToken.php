<?php
namespace Acts\CamdramSecurityBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class CamdramUserToken extends AbstractToken
{
    /**
     * @var array
     */
    private $services = array();

    /**
     * An array of all the services to which the user has expressed an intent to connect
     *
     * @var array
     */
    private $connect_to = array();

    /**
     * @var string
     */
    private $first_service_name;

    /**
     * @var string
     */
    private $last_service_name;

    /**
     * @var array
     */
    private $potential_users = array();

    /**
     * @var array
     */
    private $validated_users = array();

    /**
     * @param string $accessToken The OAuth access token
     * @param array  $roles       Roles for the token
     */
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {
        return '';
    }


    public function addService($service_name, $access_token, $user_info = array())
    {
        $this->services[$service_name] = new CamdramUserTokenService($service_name, $access_token, $user_info);
        $this->last_service_name = $service_name;
        if (empty($this->first_service_name)) $this->first_service_name = $service_name;
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getService($name)
    {
        return $this->services[$name];
    }

    public function getLastService()
    {
        if (!empty($this->last_service_name)) return $this->services[$this->last_service_name];
    }

    public function getFirstService()
    {
        if (!empty($this->first_service_name)) return $this->services[$this->first_service_name];
    }

    public function getPotentialUsers()
    {
        return $this->potential_users;
    }

    public function setPotentialUsers(array $users)
    {
        $this->potential_users = $users;
    }

    public function addPotentialUser(UserInterface $user)
    {
        return $this->potential_users[] = $user;
    }

    public function getPotentialUserCount()
    {
        return count($this->potential_users);
    }

    public function isPotentialUser(UserInterface $user)
    {
        foreach ($this->potential_users as $p_user)
        {
            if ($user->getUsername() == $p_user->getUsername()) return true;
        }
        return false;
    }

    public function isUserValidated(UserInterface $user)
    {
        return isset($this->validated_users[$user->getUsername()]);
    }

    public function setUserValidated(UserInterface $user)
    {
        $this->validated_users[$user->getUsername()] = true;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->services,
            $this->connect_to,
            $this->first_service_name,
            $this->last_service_name,
            $this->potential_users,
            $this->validated_users,
            parent::serialize()
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->services,
            $this->connect_to,
            $this->first_service_name,
            $this->last_service_name,
            $this->potential_users,
            $this->validated_users,
            $parent,
            ) = unserialize($serialized);

        parent::unserialize($parent);
    }
}