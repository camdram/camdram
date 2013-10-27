<?php
namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Acts\ExternalLoginBundle\Security\User\ExternalUserProviderInterface;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Doctrine\Common\Persistence\ObjectManager;

class ExternalLoginUserProvider implements ExternalUserProviderInterface
{
    /**
     * @var ObjectManager;
     */
    protected $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Actually loads by remote id...but has to comply with the Symfony interface
     *
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws
     */
    public function loadUserByUsername($id)
    {
        return $this->em->getRepository('ActsCamdramSecurityBundle:ExternalUser')->findOneBy(array('remote_id' => $id));
    }

    public function loadUserByServiceAndId($service, $id)
    {
        $user = $this->em->getRepository('ActsCamdramSecurityBundle:ExternalUser')->findOneBy(array(
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
        return $class === 'Acts\CamdramSecurityBundle\Security\User\ExternalLoginUser';
    }

    public function persistUser($userinfo, $service, $access_token)
    {
        $user = new ExternalUser();
        $user->setService($service);
        $user->setEmail($userinfo['email']);
        $user->setRemoteId($userinfo['id']);
        $user->setUsername($userinfo['username']);
        $user->setName($userinfo['name']);
        if (is_string($access_token)) $user->setToken($access_token);

        if ($service == 'facebook') {
            $user->setProfilePictureUrl('https://graph.facebook.com/'.$userinfo['id'].'/picture?type=large');
        }
        elseif (isset($userinfo['picture'])) {
            $user->setProfilePictureUrl($userinfo['picture']);
        }

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }


}
