<?php
namespace Acts\ExternalLoginBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Acts\ExternalLoginBundle\Entity\ExternalUser;
use Acts\ExternalLoginBundle\Security\Service\ServiceProvider;
use Doctrine\Common\Persistence\ObjectManager;

class ExternalLoginUserProvider implements UserProviderInterface
{
    /**
     * @var ObjectManager;
     */
    protected $em;

    /**
     * @var ServiceProvider
     */
    protected $serviceProvider;

    public function __construct(ObjectManager $em, ServiceProvider $serviceProvider)
    {
        $this->em = $em;
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * Actually loads by email...but has to comply with the Symfony interface
     *
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws
     */
    public function loadUserByUsername($id)
    {
        return $this->em->getRepository('ActsExternalLoginBundle:ExternalUser')->findOneBy(array('remote_id' => $id));
    }

    public function loadUserByServiceAndId($service, $id)
    {
        $user = $this->em->getRepository('ActsExternalLoginBundle:ExternalUser')->findOneBy(array(
            'service' => $service,
            'remote_id' => $id
        ));
        if (!$user) {
            throw new UsernameNotFoundException();
        }
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ExternalUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user = $this->loadUserByServiceAndId($user->getService(), $user->getRemoteId());
        return $user;
    }

    public function supportsClass($class)
    {
        return $class === 'Acts\ExternalLoginBundle\Security\User\ExternalLoginUser';
    }

    public function createUser($userinfo, $service, $access_token)
    {
        $user = new ExternalUser();
        $user->setService($service);
        $user->setEmail($userinfo['email']);
        $user->setRemoteId($userinfo['id']);
        $user->setUsername($userinfo['username']);
        $user->setName($userinfo['name']);
        $user->setToken($access_token);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

}
