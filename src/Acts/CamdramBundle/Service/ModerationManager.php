<?php
namespace Acts\CamdramBundle\Service;

use Acts\CamdramBundle\Entity\Entity;
use Acts\CamdramBundle\Service\EmailDispatcher;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\RouterInterface;

use Acts\CamdramBundle\Entity\Show;

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
    /**
     * @var \Acts\CamdramBundle\Entity\UserRepository
     */
    private $repository;

    private $dispatcher;

    public function __construct(EntityManager $entityManager, EmailDispatcher $dispatcher)
    {
        $this->repository = $entityManager->getRepository('ActsCamdramBundle:User');
        $this->dispatcher = $dispatcher;
    }

    /**
     * Determine which Entities the user is permitted to moderate.
     * @return Entities[] an array of Entities.
     */
    public function getEntitiesForModerator()
    {
        $entities = array();
        // TODO implement

        $entities[] = $entities;
        return $entities;
    }

    /**
     * Determine which Users are permitted to moderate the given Entity.
     * @return Users[] an array of Camdram Users.
     */
    public function getModeratorsForEntity(Entity $entity)
    {
        $users = array();

        if ($entity instanceof Show)
        {
            $users = $this->repository->findAdmins(AccessControlEntry::LEVEL_FULL_ADMIN);
            if ($entity->getSociety()) {
                $users = array_merge($users, $this->repository->findOrganisationAdmins($entity->getSociety()));
            }
            if ($entity->getVenue()) {
                $users = array_merge($users, $this->repository->findOrganisationAdmins($entity->getVenue()));
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
    public function emailEntityModerators(Entity $entity)
    {
        if ($entity instanceof Show) {
            $moderators = $this->getModeratorsForEntity($entity);
            $owners = $this->repository->getEntityOwners($entity);
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

