<?php
namespace Acts\CamdramSecurityBundle\Security\Acl;

use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;

use Acts\CamdramSecurityBundle\Entity\User;
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

    public function isOwner(TokenInterface $token, OwnableInterface $entity)
    {
        if ($token->getUser() instanceof ExternalUser) $user = $token->getUser()->getUser();
        elseif ($token->getUser() instanceof User) $user = $token->getUser();

        if (!isset($user) || !$user instanceof User) return false;

        return $this->repository->aceExists($user, $entity);
    }

    public function getOwners(OwnableInterface $entity)
    {
        return $this->entityManager->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($entity);
    }

    public function getEntityIdsByUser(User $user, $class)
    {
        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface('Acts\\CamdramSecurityBundle\\Security\\OwnableInterface')) {
            throw new \InvalidArgumentException(sprintf('"%s" is not an ownable class - it must implement OwnableInterface', $class));
        }

        $aces = $this->entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry')->findByUserAndType($user, $class::getAceType());
        $ids = array_map(function (AccessControlEntry $ace) {
            return $ace->getEntityId();
        }, $aces);
        return $ids;
    }

    public function getEntitiesByUser(User $user, $class)
    {
        $ids = $this->getEntityIdsByUser($user, $class);
        if (count($ids) == 0) return array();
        
        $qb = $this->entityManager->getRepository($class)->createQueryBuilder('e');
        $qb->where($qb->expr()->in('e.id', $ids));
        return $qb->getQuery()->getResult();
    }

    public function grantAccess(OwnableInterface $entity, User $user, User $granter)
    {
        $ace = new AccessControlEntry;
        $ace->setUser($user);

        $ace->setEntityId($entity->getId())
            ->setCreatedAt(new \DateTime)
            ->setGrantedBy($granter)
            ->setGrantedAt(new \DateTime)
            ->setType($entity->getAceType());

        $this->entityManager->persist($ace);
        $this->entityManager->flush();
    }
}
