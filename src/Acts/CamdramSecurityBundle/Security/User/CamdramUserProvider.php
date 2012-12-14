<?php
namespace Acts\CamdramSecurityBundle\Security\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use  Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Security\Exception\IdentityNotFoundException;
use Doctrine\ORM\EntityManager;

class CamdramUserProvider implements UserProviderInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager;
     */
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /**
     * Actually loads by id...but has to comply with the Symfony interface
     *
     * @param string $username
     * @return \Symfony\Component\Security\Core\User\UserInterface
     * @throws
     */
    public function loadUserByUsername($id)
    {
        $user = $this->em->getRepository('ActsCamdramBundle:User')->findOneById($id);
        if ($user) return $user;
        else throw UnsupportedUserException();
    }

    public function loadUserByServiceAndUser($service, $info)
    {
        if ($service == 'local') {
            return $this->em->getRepository('ActsCamdramBundle:User')->findOneById($info['id']);
        }

        $res = $this->em->createQuery('SELECT u FROM ActsCamdramBundle:User u JOIN u.identities i WHERE i.service = :service AND (i.remote_id = :id OR i.remote_user = :username)')
                ->setParameter('service', $service)
                ->setParameter('id', $info['id'])
                ->setParameter('username', $info['username'])
                ->getResult();

        if (count($res) > 0) {
            return $res[0];
        }
        else {
            throw new IdentityNotFoundException(sprintf('An identity cannot be found for "%s" and credentials %i/%s', $service, $info['id'], $info['username']));
        }
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
}
