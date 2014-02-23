<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Service\ModerationManager;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\Persistence\ObjectManager;

class ShowListener
{
    private $moderationManager;

    public function __construct(ModerationManager $moderationManager)
    {
        $this->moderationManager = $moderationManager;
    }

    private function updateFields(Show $show, ObjectManager $om)
    {
        //ensure all the associated performances are also saved
        foreach ($show->getPerformances() as $performance) {
            $performance->setShow($show);
            $om->persist($performance);
        }

        //ensure the venue attached to the show and to the performances are consistent
        $show->updateVenues();
        //ensure the start_at and end_at fields are equal to the start and end of the first and last performances
        $show->updateTimes();
    }

    public function prePersist(Show $show, LifecycleEventArgs $event)
    {
        $this->updateFields($show, $event->getObjectManager());
    }

    public function postPersist(Show $show, LifecycleEventArgs $event)
    {
        $this->moderationManager->autoApproveOrEmailModerators($show);
    }

    public function preUpdate(Show $show, LifecycleEventArgs $event)
    {
        $this->updateFields($show, $event->getObjectManager());
    }
} 