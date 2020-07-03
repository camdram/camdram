<?php

namespace Acts\CamdramSecurityBundle\Security\User;

use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     * Actually loads by email...but has to comply with the Symfony interface
     *
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function loadUserByUsername($email)
    {
        return $this->em->getRepository(User::class)->findOneByEmail($email);
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

    private function loadOrCreateExternalUser(UserResponseInterface $response): ExternalUser
    {
        $service = $response->getResourceOwner()->getName();
        $username = $response->getUsername();
        $external = $this->em->getRepository(ExternalUser::class)->findOneBy(array(
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
        $user = $this->em->getRepository(User::class)->findByExternalUser($service, $username);

        // Attempt to auto-link using an email match
        if (!$user && $response->getEmail()) {
            $user = $this->em->getRepository(User::class)->findOneByEmail($response->getEmail());
            if ($user) {
                $external = $this->loadOrCreateExternalUser($response);
                $external->setUser($user);
                $this->em->flush();
            }
        }

        if (!$user) {
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $response->getUsername()));
        }

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Expected a Camdram User, but got "%s".', get_class($user)));
        }

        $external = $this->loadOrCreateExternalUser($response);
        $external->setUser($user);
        $this->em->flush();
    }
}
