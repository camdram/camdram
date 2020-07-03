<?php

namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Acts\CamdramSecurityBundle\Security\OwnableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent;

class AclProvider
{
    /**
     * @var \Acts\CamdramSecurityBundle\Entity\AccessControlEntryRepository
     */
    private $repository;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $entityManager->getRepository(AccessControlEntry::class);
    }

    public function isOwner($user, OwnableInterface $entity)
    {
        if (is_null($user) || !$user instanceof User) {
            return false;
        }

        return $this->repository->aceExists($user, $entity);
    }

    public function getOwners(OwnableInterface $entity)
    {
        return $this->entityManager->getRepository(User::class)->getEntityOwners($entity);
    }

    // user -> ace ->society -> (join table) -> show
    //             ↳  venue  -> performance  ⮥
    public function getOwnersOfOwningSocs(Show $show): array
    {
        return $this->getOwnersOfOwningOrgs($show, 'society');
    }

    public function getOwnersOfOwningVens(Show $show): array
    {
        return $this->getOwnersOfOwningOrgs($show, 'venue');
    }

    public function getOwnersOfOwningOrgs(Show $show, string $type = null): array
    {
        if ($type && $type !== 'society' && $type !== 'venue') {
            throw new \Exception("Type may not be ". $type);
        }
        $socs = ($type === 'venue') ? [] : $show->getSocieties()->map(
            function($s) { return $s->getId(); })->toArray();

        $query = $this->entityManager->createQuery(
            "SELECT u FROM ActsCamdramSecurityBundle:User u WHERE EXISTS
            (SELECT ace FROM ActsCamdramSecurityBundle:AccessControlEntry ace WHERE ace.user = u AND (
                (ace.type = 'society' AND ace.entityId IN (:socs))"
            . ($type === 'society' ? '))' :
            "   OR (ace.type = 'venue' AND ace.entityId IN
                (SELECT IDENTITY(perf.venue) FROM ActsCamdramBundle:Performance perf
                 WHERE perf.show = :show AND perf.venue IS NOT NULL))))")
            )->setParameter('socs', $socs);
        if ($type !== 'society') $query->setParameter('show', $show);

        return $query->getResult();
    }

    public function getAdmins($min_level = AccessControlEntry::LEVEL_FULL_ADMIN)
    {
        return $this->entityManager->getRepository(User::class)->findAdmins($min_level);
    }

    public function getOrganisationsByUser(User $user): array
    {
        $socs = $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Society')->createQueryBuilder('s')
            ->where("EXISTS (SELECT ace FROM \\Acts\\CamdramSecurityBundle\\Entity\\AccessControlEntry ace ".
                    "WHERE ace.entityId = s.id AND ace.type = 'society' AND ace.user = :user)")
            ->setParameter('user', $user)->getQuery()->getResult();
        $vens = $this->entityManager->getRepository('\Acts\CamdramBundle\Entity\Venue')->createQueryBuilder('v')
            ->where("EXISTS (SELECT ace FROM \\Acts\\CamdramSecurityBundle\\Entity\\AccessControlEntry ace ".
                    "WHERE ace.entityId = v.id AND ace.type = 'venue' AND ace.user = :user)")
            ->setParameter('user', $user)->getQuery()->getResult();

        return array_merge($socs, $vens);
    }

    public function getEntityIdsByUser(User $user, $class)
    {
        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface('Acts\\CamdramSecurityBundle\\Security\\OwnableInterface')) {
            throw new \InvalidArgumentException(sprintf('"%s" is not an ownable class - it must implement OwnableInterface', $class));
        }

        $aces = $this->entityManager->getRepository(AccessControlEntry::class)->findByUserAndType($user, $class::getAceType());
        $ids = array_map(function (AccessControlEntry $ace) {
            return $ace->getEntityId();
        }, $aces);

        return $ids;
    }

    /**
     * @phpstan-template T
     * @phpstan-param class-string<T> $class
     * @phpstan-return T[]
     */
    public function getEntitiesByUser(User $user, $class)
    {
        $ids = $this->getEntityIdsByUser($user, $class);
        if (count($ids) == 0) {
            return array();
        }

        $qb = $this->entityManager->getRepository($class)->createQueryBuilder('e');
        $qb->where($qb->expr()->in('e.id', $ids));

        return $qb->getQuery()->getResult();
    }

    /**
     * Grant access to a resource.
     *
     * Immediately grant access to a resoure. Creates a new ACE in the
     * database, and dispatches a Camdram-specific event that is used
     * to trigger sending of emails.
     */
    public function grantAccess(OwnableInterface $entity, User $user, User $granter = null)
    {
        $ace = new AccessControlEntry();
        $ace->setUser($user);
        $user->addAce($ace);

        $ace->setEntityId($entity->getId())
            ->setCreatedAt(new \DateTime())
            ->setGrantedBy($granter)
            ->setType($entity->getAceType());

        $this->entityManager->persist($ace);
        $this->entityManager->flush();

        /* Send a Camdram-specific event that should trigger an email
                 * notification.
                 */
        $this->eventDispatcher->dispatch(
            new AccessControlEntryEvent($ace),
            CamdramSecurityEvents::ACE_CREATED
        );
    }

    /**
     * Revoke access to the entity.
     */
    public function revokeAccess(OwnableInterface $entity, User $user, User $revoker = null)
    {
        $ace_repo = $this->repository;

        if ($ace = $ace_repo->findAce($user, $entity)) {
            $this->entityManager->remove($ace);
            $this->entityManager->flush();
        }

        if ($entity instanceof Show) {
            /* Also remove any requests to be a show admin, if they existed. */
            $request = $ace_repo->findAceRequest($user, $entity);
            if ($request != null) {
                $this->entityManager->remove($request);
                $this->entityManager->flush();
            }
        }
    }

    public function grantAdmin(User $user, $level = AccessControlEntry::LEVEL_FULL_ADMIN)
    {
        $aceRepo = $this->repository;
        $qb = $aceRepo->createQueryBuilder('a');
        $aces = $qb->where('a.type = :type')
            ->andWhere('a.user = :user')
            ->setParameter('type', 'security')
            ->setParameter('user', $user)
            ->getQuery()->getResult();
        foreach ($aces as $ace) {
            if ($ace->getEntityId() == $level) {
                return;
            }
            else {
                $this->entityManager->remove($ace);
                $this->entityManager->flush();
            }
        }

        $ace = new AccessControlEntry();
        $ace->setUser($user);
        $user->addAce($ace);

        $ace->setEntityId($level)
            ->setCreatedAt(new \DateTime())
            ->setType('security');

        $this->entityManager->persist($ace);
        $this->entityManager->flush();
    }

    public function revokeAdmin(User $user)
    {
        $aceRepo = $this->repository;
        $qb = $aceRepo->createQueryBuilder('a');
        $aces = $qb->where('a.type = :type')
            ->andWhere('a.user = :user')
            ->setParameter('type', 'security')
            ->setParameter('user', $user)
            ->getQuery()->getResult();

        foreach ($aces as $ace) {
            $this->entityManager->remove($ace);
        }

        $this->entityManager->flush();
    }

    /**
     * Approve a request to access a show.
     *
     * Unlike other entities, (societies and venues), users can request
     * to access a show. This function approves the request, and dispatches
     * a Camdram-specific event that is used to trigger sending of emails.
     */
    public function approveShowAccess(Show $show, User $user, User $granter)
    {
        $ace_repo = $this->repository;
        $existing_ace = $ace_repo->findAce($user, $show);
        /* Don't add a new ACE if the user is already able to access
         * the resource.
         */
        if ($existing_ace != null) return;

        $ace = $ace_repo->findAceRequest($user, $show);
        if ($ace == null) return;

        $ace->setGrantedBy($granter)->setType('show');
        $this->entityManager->persist($ace);
        $this->entityManager->flush();

        /* Send a Camdram-specific event that should trigger an email
         * notification.
         */
        $this->eventDispatcher->dispatch(
                new AccessControlEntryEvent($ace),
                CamdramSecurityEvents::ACE_CREATED
            );
    }
}
