<?php

namespace Acts\CamdramLegacyBundle\EventListener;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramLegacyBundle\Entity\ShowRef;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Gedmo\Sluggable\Util as Sluggable;

/**
 * Class ShowRefCreator
 *
 * Creates a show-ref based on the show's slug. It isn't used by Camdram v2 but is required for shows to appear on v1
 */
class ShowRefCreator
{
    public function prePersist(Show $show, LifecycleEventArgs $event)
    {
        if (!$show->getPrimaryRef()) {
            $refname = Sluggable\Urlizer::urlize($show->getName(), '_');
            if ($show->getStartAt()) {
                $year = $show->getStartAt()->format('y');
                $refname = $year.'/'.$refname;
            }

            $ref = new ShowRef();
            $ref->setShow($show);
            $ref->setRef($refname);
            $show->setPrimaryRef($ref);
        }
    }
}
