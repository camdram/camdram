<?php

namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Manage the moderation of Entities
 *
 * Entities created on Camdram are moderated by users to ensure the contents
 * of Camdram is correct. The moderation manager determines who can moderate
 * a particular Entity and provides methods for informing the moderators.
 *
 * Moderation in this context refers to granting or denying the publicising of
 * an Entity on Camdram. The content of that Entity is controlled elsewhere.
 */
class ModerationManager
{
    private $entityManager;

    private $dispatcher;

    private $aclProvider;

    private $authorizationChecker;

    private $tokenStorage;

    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        EmailDispatcher $dispatcher,
                                AclProvider $aclProvider,
        AuthorizationCheckerInterface $authorizationChecker,
                                TokenStorageInterface $tokenStorage,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->aclProvider = $aclProvider;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * Determine which Entities the user is permitted to moderate.
     *
     * @return array An array of entities.
     */
    public function getEntitiesToModerate()
    {
        $show_repo = $this->entityManager->getRepository('ActsCamdramBundle:Show');
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $show_repo->findUnauthorised();
        } elseif ($this->authorizationChecker->isGranted('ROLE_USER')) {
            $ids = $this->aclProvider->getOrganisationIdsByUser($this->tokenStorage->getToken()->getUser());
            $orgs = $this->entityManager->getRepository('ActsCamdramBundle:Organisation')->findById($ids);
            $entities = array();
            foreach ($orgs as $org) {
                if ($org instanceof Society) {
                    $entities = array_merge($entities, $show_repo->findUnauthorisedBySociety($org));
                } elseif ($org instanceof Venue) {
                    $entities = array_merge($entities, $show_repo->findUnauthorisedByVenue($org));
                }
            }

            return $entities;
        } else {
            return array();
        }
    }

    /**
     * Determine which Users are permitted to moderate the given Entity.
     *
     * @return Users[] an array of Camdram Users.
     */
    public function getModeratorsForEntity($entity)
    {
        $users = array();
        $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');

        if ($entity instanceof Show) {
            foreach ($entity->getSocieties() as $society) {
                $users = array_merge($users, $repo->getEntityOwners($society));
            }
            if ($entity->getVenue()) {
                $users = array_merge($users, $repo->getEntityOwners($entity->getVenue()));
            }
        }
        if (count($users) == 0) {
            //If there is no venue/society or both have zero admins, then the Camdram admins become the moderators
            $users = $this->getModeratorAdmins();
        }

        return $users;
    }

    public function getModeratorAdmins()
    {
        $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');

        return $repo->findAdmins(AccessControlEntry::LEVEL_FULL_ADMIN);
    }

    public function approveEntity(Show $entity)
    {
        if ($entity instanceof Show && !$entity->getAuthorised()) {
            $entity->setAuthorised(true);

            $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');
            $owners = $repo->getEntityOwners($entity);
            $authorisedBy = $this->tokenStorage->getToken()->getUser();
            $this->dispatcher->sendShowApprovedEmail($entity, $owners, $authorisedBy);
            $this->logger->info('Show authorised', array('id' => $entity->getId(), 'name' => $entity->getName()));
        }
    }

    public function autoApproveOrEmailModerators(Show $entity)
    {
        if (!$entity->getAuthorised()) {
            if ($this->authorizationChecker->isGranted('APPROVE', $entity)) {
                //The current user is able to approve the show, so approve it straight away.
                $this->approveEntity($entity);
            } else {
                //Else Send an email
                $this->emailEntityModerators($entity);
            }
        }
    }

    /**
     * Email moderators for that Entity.
     *
     * Inform the moderators via email that the Entity needs to be moderated.
     * This action _should_ be invoked when a new Entity that needs moderation
     * is created. This action _may_ be called again for some other reason, e.g.
     * if an Entity hasn't been moderated after a period of time a reminder
     * email may need to be sent.
     */
    public function emailEntityModerators($entity)
    {
        if ($entity instanceof Show) {
            $moderators = $this->getModeratorsForEntity($entity);
            $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');
            if ($this->tokenStorage->getToken()) {
                $owners = array($this->tokenStorage->getToken()->getUser());
            } else {
                $owners = $repo->getEntityOwners($entity);
            }
            $admins = $this->getModeratorAdmins();
            /* Construct a list of email addresses to add to the 'To' field of the
             * email.
             * TODO Improve upon Camdram's current approach of emailing all moderators
             * by explaining _why_ the moderator is receiving this email. This needs
             * thought about the prescedence of a User's role. When emailing it'd be
             * best to use a User's lowest privelage as the reason for receiving an
             * email, e.g. a Camdram admin may also be a Society's admin, but send
             * their email as if they are just the latter.
             */
            $this->dispatcher->sendShowCreatedEmail($entity, $owners, $moderators, $admins);
            $this->logger->info('Authorisation e-mail sent', array('id' => $entity->getId(), 'name' => $entity->getName()));
        }
    }

    public function notifySocietyChanged($entity)
    {
        if ($entity instanceof Show) {
            $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');
            $moderators = [];
            foreach ($entity->getSocieties() as $society) {
                $moderators = array_merge($moderators, $repo->getEntityOwners($society));
            }
            if (count($moderators) > 0) {
                if ($this->tokenStorage->getToken()) {
                    $owners = array($this->tokenStorage->getToken()->getUser());
                } else {
                    $owners = $repo->getEntityOwners($entity);
                }

                $this->dispatcher->sendShowSocietyChangedEmail($entity, $owners, $moderators);
                $this->logger->info('Society changed e-mail sent', array('id' => $entity->getId(), 'name' => $entity->getName()));
            }
        }
    }

    public function notifyVenueChanged($entity)
    {
        if ($entity instanceof Show) {
            $repo = $this->entityManager->getRepository('ActsCamdramSecurityBundle:User');
            $moderators = $repo->getEntityOwners($entity->getVenue());
            if (count($moderators) > 0) {
                if ($this->tokenStorage->getToken()) {
                    $owners = array($this->tokenStorage->getToken()->getUser());
                } else {
                    $owners = $repo->getEntityOwners($entity);
                }

                $this->dispatcher->sendShowVenueChangedEmail($entity, $owners, $moderators);
                $this->logger->info('Venue changed e-mail sent', array('id' => $entity->getId(), 'name' => $entity->getName()));
            }
        }
    }
}
