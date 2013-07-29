<?php
namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramSecurityBundle\Entity\Group;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository;

class AclProvider
{
    /**
     * @var \Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository
     */
    private $repository;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');
    }

    public function isOwner(TokenInterface $token, Entity $entity)
    {
        if (!$token->getUser() instanceof User) return false;

        /** @var $user User */
        $user = $token->getUser();

        $users = array($user);
        $groups = $user->getGroups();

        return $this->repository->aceExists($users, $groups, $entity);
    }

    public function getEntitiesByGroup(Group $group, $class_name = null)
    {
        return $this->entityManager->getRepository('ActsCamdramBundle:Entity')->getByGroup($group, $class_name);
    }

    public function getEntitiesByUser(User $user, $class_name = null)
    {
        return $this->entityManager->getRepository('ActsCamdramBundle:Entity')->getByUser($user, $class_name);
    }

    public function grantAccess(Entity $entity, $role, User $granter)
    {
        $ace = new AccessControlEntry;
        if ($role instanceof User) {
            $ace->setUser($role);
        }
        elseif ($role instanceof Group) {
            $ace->setGroup($role);
        }
        else {
            return;
        }

        $ace->setEntity($entity);

        $ace->setCreatedAt(new \DateTime);
        $ace->setGrantedBy($granter)
            ->setGrantedAt(new \DateTime);

        $this->entityManager->persist($ace);
        $this->entityManager->flush();
    }
}

