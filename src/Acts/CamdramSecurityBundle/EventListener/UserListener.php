<?php

namespace Acts\CamdramSecurityBundle\EventListener;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Service\TokenGenerator;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class UserListener
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $emailDispatcher;
    private $tokenGenerator;

    public function __construct(EntityManager $em, EmailDispatcher $emailDispatcher, TokenGenerator $tokenGenerator)
    {
        $this->entityManager = $em;
        $this->emailDispatcher = $emailDispatcher;
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * Delete any pending access tokens given to this user, and grant access to
     * those resources in turn.
     */
    public function postPersist(User $user, LifecycleEventArgs $event)
    {
        $pending_aces = $this->entityManager->getRepository('ActsCamdramSecurityBundle:PendingAccess')
                            ->findByEmail($user->getEmail());
        foreach ($pending_aces as $pending) {
            $ace = new AccessControlEntry();
            $ace->setUser($user)
                ->setEntityId($pending->getRid())
                ->setCreatedAt(new \DateTime())
                ->setGrantedBy($pending->getIssuer())
                ->setGrantedAt(new \DateTime())
                ->setType($pending->getType());

            $this->entityManager->persist($ace);
            $this->entityManager->remove($pending);
        }
        $this->entityManager->flush();
    }

    public function preUpdate(User $user, PreUpdateEventArgs $event)
    {
        if ($event->hasChangedField('email')) {
            $token = $this->tokenGenerator->generateEmailConfirmationToken($user);
            $this->emailDispatcher->sendEmailVerifyEmail($user, $token);
        }
    }
}
