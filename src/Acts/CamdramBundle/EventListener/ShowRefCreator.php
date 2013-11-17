<?php
namespace Acts\CamdramBundle\EventListener;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\ShowRef;
use Acts\CamdramBundle\Event\EntityEvent;
use Doctrine\ORM\EntityManager;

/**
 * Class ShowRefCreator
 *
 * Creates a show-ref based on the show's slug. It isn't used by Camdram v2 but is required for shows to appear on v1
 *
 * @package Acts\CamdramBundle\EventListener
 */
class ShowRefCreator
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function onShowCreated(EntityEvent $event)
    {
        $show = $event->getEntity();
        if (!$show instanceof Show)  return;

        if (!$show->getPrimaryRef()) {
            $year = $show->getStartAt()->format('y');

            $ref = new ShowRef();
            $ref->setShow($show);
            $ref->setRef($year.'/'.$show->getSlug());
            $this->entityManager->persist($ref);
            $show->setPrimaryRef($ref);
            $this->entityManager->flush();
        }
    }

}