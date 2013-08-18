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
        $this->services[] = new CamdramUserTokenService($service_name, $access_token, $user_info);
    }

    public function getServices()
    {
        return $this->services;
    }

    public function getService($name)
    {
        return $this->services[$name];
    }

    public function getServiceByName($name)
    {
        foreach ($this->services as $service) {
            if ($service->getName() == $name) return $service;
        }
    }

    public function getLastService()
    {
        return end($this->services);
    }

    public function getFirstService()
    {
        return reset($this->services);
    }

    public function getPotentialUsers()
    {
        return $this->potential_users;
    }

    public function removeService($service)
    {
        foreach ($this->services as $id => $s) {
            if ($service == $s) {
                unset($this->services[$id]);
            }
        }
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

    public function removeLastService()
    {
        array_pop($this->services);
        $this->last_service_name = null;
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

    public function cleanIdentities()
    {
        //Delete any services that don't correspond to this user
        foreach ($this->services as $id => $service) {
            $i = $this->getUser()->getIdentityByServiceName($service->getName());
            if (!$i || $i->getRemoteId() == $service->getUserInfo('id')
                || $i->getRemoteUser() == $service->getUserInfo('username')) {
                unset($this->services[$id]);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->services,
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
            $this->potential_users,
            $this->validated_users,
            $parent,
            ) = unserialize($serialized);

        parent::unserialize($parent);
    }
}