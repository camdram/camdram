<?php

namespace Acts\CamdramSecurityBundle\Security\Acl;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Acts\CamdramSecurityBundle\Entity\ExternalUser;
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
         * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
         */
    private $eventDispatcher = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->repository = $entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');
    }

    public function isOwner($user, OwnableInterface $entity)
    {
        if ($user instanceof ExternalUser) {
            $user = $user->getUser();
        }

        if (is_null($user) || !$user instanceof User) {
            return false;
        }

        return $this->repository->aceExists($user, $entity);
    }

    public function getOwners(OwnableInterface $entity)
    {
        return $this->entityManager->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($entity);
    }

    public function hasOwners(OwnableInterface $entity)
    {
        return $this->entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry')->countAces($entity);
    }

    public function getAdmins($min_level = AccessControlEntry::LEVEL_FULL_ADMIN)
    {
        return $this->entityManager->getRepository('ActsCamdramSecurityBundle:User')->findAdmins($min_level);
    }

    public function getOrganisationIdsByUser(User $user)
    {
        $aces = $this->entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry')->findByUserAndType($user, 'society');
        $ids = array_map(function (AccessControlEntry $ace) {
            return $ace->getEntityId();
        }, $aces);

        return $ids;
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
    public function grantAccess(OwnableInterface $entity, User $user, User $granter)
    {
        $ace = new AccessControlEntry();
        $ace->setUser($user);

        $ace->setEntityId($entity->getId())
            ->setCreatedAt(new \DateTime())
            ->setGrantedBy($granter)
            ->setGrantedAt(new \DateTime())
            ->setType($entity->getAceType());

        $this->entityManager->persist($ace);
        $this->entityManager->flush();

        /* Send a Camdram-specific event that should trigger an email
                 * notification.
                 */
        $this->eventDispatcher->dispatch(
            CamdramSecurityEvents::ACE_CREATED,
            new AccessControlEntryEvent($ace)
        );
    }

    /**
     * Revoke access to the entity.
     */
    public function revokeAccess(OwnableInterface $entity, User $user, User $revoker)
    {
        $ace_repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');
        $ace = $ace_repo->findAce($user, $entity);
        /* Don't re-revoke an ACE. */
        if (($ace != null) && ($ace->getRevokedBy() == null)) {
            $ace->setRevokedBy($revoker)
                ->setRevokedAt(new \DateTime());
            $this->entityManager->persist($ace);
            $this->entityManager->flush();
        }
        if ($entity->getAceType() == 'show') {
            /* Remove any requests to be a show admin, if they existed. */
            $request = $ace_repo->findAceRequest($user, $entity);
            if ($request != null) {
                $this->entityManager->remove($request);
                $this->entityManager->flush();
            }
        }
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
        $persist = false;
        $ace_repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');
        $existing_ace = $ace_repo->findAce($user, $show);
        /* Don't add a new ACE if the user is already able to access
         * the resource.
         */
        if ($existing_ace == null) {
            $ace = $ace_repo->findAceRequest($user, $show);
            if ($ace != null) {
                $ace->setGrantedBy($granter)
                    ->setGrantedAt(new \DateTime())
                    ->setType('show');
                $persist = true;
            }
        } elseif ($existing_ace->getRevokedBy() != null) {
            /* Assume that we're undoing the previous revokation. */
            $existing_ace->setRevokedBy(null)
                ->setRevokedAt(null)
                ->setGrantedBy($granter)
                ->setGrantedAt(new \DateTime());
            $persist = true;
        }

        if ($persist == true) {
            $this->entityManager->persist($ace);
            $this->entityManager->flush();

            /* Send a Camdram-specific event that should trigger an email
                             * notification.
                             */
            $this->eventDispatcher->dispatch(
                    CamdramSecurityEvents::ACE_CREATED,
                    new AccessControlEntryEvent($ace)
                );
        }
    }
}
