<?php

namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;

class CamdramUserProvider implements
    UserProviderInterface,
        OAuthAwareUserProviderInterface,
    AccountConnectorInterface
{
    /**
     * @var EntityManagerInterface;
     */
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Actually loads by id...but has to comply with the Symfony interface
     *
     * @param string $username
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     *
     * @throws
     */
    public function loadUserByUsername($id)
    {
        return $this->em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($id);
    }

    public function updateAccessToken(User $user, $service, $access_token)
    {
        $s = $user->getIdentityByServiceName($service);
        if ($s) {
            $s->loadAccessToken($access_token);
            $this->em->flush();
        }
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Acts\CamdramSecurityBundle\Entity\User';
    }

    private function loadOrCreateExternalUser(UserResponseInterface $response)
    {
        $service = $response->getResourceOwner()->getName();
        $username = $response->getUsername();
        $external = $this->em->getRepository('ActsCamdramSecurityBundle:ExternalUser')->findOneBy(array(
            'service' => $service,
            'username' => $username
        ));

        if ($external) {
            $external->setToken($response->getAccessToken());
        } else {
            $external = new ExternalUser();
            $external->setService($service);
            $external->setEmail($response->getEmail());
            $external->setUsername($username);
            $external->setName($response->getRealName());
            $external->setProfilePictureUrl($response->getProfilePicture());
            $external->setToken($response->getAccessToken());
            $this->em->persist($external);
        }

        return $external;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $service = $response->getResourceOwner()->getName();
        $username = $response->getUsername();

        // First try an exact match
        $user = $this->em->getRepository('ActsCamdramSecurityBundle:User')->findByExternalUser($service, $username);
        // Attempt to auto-link using an email match
        if (!$user && $response->getEmail()) {
            $user = $this->em->getRepository('ActsCamdramSecurityBundle:User')->findOneByEmail($response->getEmail());
            $external = $this->loadOrCreateExternalUser($response);
            $external->setUser($user);
            $this->em->flush();
        }

        if (!$user) {
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $response->getUsername()));
        }

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected a Camdram User, but got "%s".', get_class($user)));
        }

        $external = $this->loadOrCreateExternalUser($response);
        $external->setUser($user);
        $this->em->flush();
    }
}
