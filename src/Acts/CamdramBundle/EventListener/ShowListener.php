<?php

namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\ShowSlug;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Service\WeekManager;
use Acts\CamdramBundle\Service\ModerationManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class ShowListener
{
    private $moderationManager;

    private $weekManager;

    public function __construct(ModerationManager $moderationManager, WeekManager $weekManager)
    {
        $this->moderationManager = $moderationManager;
        $this->weekManager = $weekManager;
    }

    public function prePersist(Show $show, LifecycleEventArgs $event)
    {
    }

    public function postLoad(Show $show, LifecycleEventArgs $event)
    {
        // Looking up week names whenever any show is loaded is expensive.
        // Instead injecting the weekManager into $show (despite this being
        // considered an "antipattern")
        $show->setWeekManager($this->weekManager);
    }

    public function postPersist(Show $show, LifecycleEventArgs $event)
    {
        $this->moderationManager->autoApproveOrEmailModerators($show);
    }

    public function preUpdate(Show $show, PreUpdateEventArgs $event)
    {
        $em = $event->getObjectManager();
        $uow  = $em->getUnitOfWork();
        $meta = $em->getClassMetadata(get_class($show));
        $uow->recomputeSingleEntityChangeSet($meta, $show);

        //ensure all the associated performances are also saved
        $performanceMeta = $em->getClassMetadata('ActsCamdramBundle:Performance');
        foreach ($show->getPerformances() as $performance) {
            $performance->setShow($show);
            $uow->recomputeSingleEntityChangeSet($performanceMeta, $performance);
        }

        $this->sendChangeEmails($show, $event);

        if ($event->hasChangedField('slug')) {
            $this->manageSlugChange($show, $event);
        }
    }

    private function sendChangeEmails(Show $show, PreUpdateEventArgs $event)
    {
        $authorisationEmailSent = false;

        if ($event->hasChangedField('societies_display_list') && ! $show->getSocieties()->isEmpty()) {
            // Can't access changes to the societies Association here, so
            // it's neccessary to parse the JSON.
            $socs_data = $event->getEntityChangeSet()['societies_display_list'];
            if (is_array($socs_data[0]) && is_array($socs_data[1])) {
                $socs_data = [array_filter($socs_data[0], 'is_int'), array_filter($socs_data[1], 'is_int')];
                if (array_diff($socs_data[0], $socs_data[1]) || array_diff($socs_data[1], $socs_data[0])) {
                    if ($show->getAuthorised()) {
                        $this->moderationManager->notifySocietyChanged($show);
                    } else {
                        $this->moderationManager->autoApproveOrEmailModerators($show);
                        $authorisationEmailSent = true;
                    }
                }
            }
        }

        if ($event->hasChangedField('venue') && $show->getVenue() instanceof Venue) {
            if ($show->getAuthorised()) {
                $this->moderationManager->notifyVenueChanged($show);
            } elseif (!$authorisationEmailSent) {
                $this->moderationManager->autoApproveOrEmailModerators($show);
            }
        }
    }

    /**
     * Saves the old and new slugs after renaming.
     */
    private function manageSlugChange($show, $event) {
        $em = $event->getEntityManager();
        $slugRepo = $em->getRepository('ActsCamdramBundle:ShowSlug');
        $oldSlug = $slugRepo->findOneBySlug($event->getOldValue("slug"));
        if (!$oldSlug) {
            // Make new slug for outgoing URL.
            $oldSlug = new ShowSlug();
            $oldSlug->setShow($show);
            $oldSlug->setSlug($event->getOldValue("slug"));
            $oldSlug->setCreatedDate(new \DateTime());
            $em->persist($oldSlug);
            $em->flush();
        }

        $newSlug = $slugRepo->findOneBySlug($event->getNewValue("slug"));
        if (!$newSlug) {
            // Make new slug for new URL.
            $newSlug = new ShowSlug();
            $newSlug->setShow($show);
            $newSlug->setSlug($event->getNewValue("slug"));
            $newSlug->setCreatedDate(new \DateTime());
            $em->persist($newSlug);
            $em->flush();
        } else if ($newSlug->getShowId() != $show->getId()) {
            // Allow slugs to be repurposed, e.g. if a show is deleted and recreated.
            // So for permanent links /shows/by-id/ remains the correct approach.
            // Checks should already have been done for a live duplicate slug, i.e. in
            // acts_shows.
            $newSlug->setShowId($show->getId());
            $newSlug->setCreatedDate(new \DateTime());
            $em->flush();
        }
    }
}
