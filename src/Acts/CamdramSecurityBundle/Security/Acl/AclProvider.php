<?php
namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramBundle\Entity\Orgnisation;
use Acts\CamdramBundle\Entity\Show;
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

        return $this->repository->aceExists($user, $entity);
    }

    public function getEntitiesByUser(User $user, $class_name = null)
    {
        return $this->entityManager->getRepository('ActsCamdramBundle:Entity')->getByUser($user, $class_name);
    }

    public function grantAccess(Entity $entity, User $user, User $granter)
    {
        $ace = new AccessControlEntry;
        $ace->setUser($user);

        $ace->setEntity($entity)
            ->setCreatedAt(new \DateTime)
            ->setGrantedBy($granter)
            ->setGrantedAt(new \DateTime);

        if ($entity instanceof Show) {
            $ace->setType('show');
        }
        elseif ($entity instanceof Organisation) {
            $ace->setType('society');
        }

        $this->entityManager->persist($ace);
        $this->entityManager->flush();
    }
}

