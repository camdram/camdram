<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\User;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

use Acts\CamdramBundle\Entity\Show;
use Symfony\Component\Security\Core\SecurityContextInterface;

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

    private $securityContext;

    public function __construct(EntityManager $entityManager, EmailDispatcher $dispatcher, AclProvider $aclProvider, SecurityContextInterface $context)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->aclProvider = $aclProvider;
        $this->securityContext = $context;
    }

    /**
     * Determine which Entities the user is permitted to moderate.
     * @return array An array of entities.
     */
    public function getEntitiesToModerate()
    {
        $show_repo = $this->entityManager->getRepository('ActsCamdramBundle:Show');
        if ($this->securityContext->isGranted('ROLE_ADMIN')) {
            return $show_repo->findUnauthorised();
        }
        elseif ($this->securityContext->isGranted('ROLE_USER')) {
            $ids = $this->aclProvider->getOrganisationIdsByUser($this->securityContext->getToken()->getUser());
            $orgs = $this->entityManager->getRepository('ActsCamdramBundle:Organisation')->find($ids);
            $entities = array();
            foreach ($orgs as $org) {
                if ($org instanceof Society) {
                    $entities = array_merge($entities, $show_repo->findUnauthorisedBySociety($org));
                }
                elseif ($org instanceof Venue) {
                    $entities = array_merge($entities, $show_repo->findUnauthorisedByVenue($org));
                }
            }
            return $entities;
        }
        else {
            return array();
        }
    }

    /**
     * Determine which Users are permitted to moderate the given Entity.
     * @return Users[] an array of Camdram Users.
     */
    public function getModeratorsForEntity($entity)
    {
        $users = array();
        $repo = $this->entityManager->getRepository('ActsCamdramBundle:User');

        if ($entity instanceof Show)
        {
            $users = $repo->findAdmins(AccessControlEntry::LEVEL_FULL_ADMIN);
            if ($entity->getSociety()) {
                $users = array_merge($users, $repo->findOrganisationAdmins($entity->getSociety()));
            }
            if ($entity->getVenue()) {
                $users = array_merge($users, $repo->findOrganisationAdmins($entity->getVenue()));
            }
        }

        return $users;
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
            $repo = $this->entityManager->getRepository('ActsCamdramBundle:User');
            $owners = $repo->getEntityOwners($entity);
            /* Construct a list of email addresses to add to the 'To' field of the
             * email.
             * TODO Improve upon Camdram's current approach of emailing all moderators
             * by explaining _why_ the moderator is receiving this email. This needs
             * thought about the prescedence of a User's role. When emailing it'd be
             * best to use a User's lowest privelage as the reason for receiving an
             * email, e.g. a Camdram admin may also be a Society's admin, but send
             * their email as if they are just the latter.
             */
            $this->dispatcher->sendShowCreatedEmail($entity, $owners, $moderators);
        }
    }
}

