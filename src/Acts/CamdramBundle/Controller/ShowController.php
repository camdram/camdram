<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\ShowType;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("Show")
 */
class ShowController extends AbstractRestController
{

    protected $class = 'Acts\\CamdramBundle\\Entity\\Show';

    protected $type = 'show';

    protected $type_plural = 'shows';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
    }

    /**
     * Perform some data sanity checks before saving
     *
     * @param mixed $entity
     * @param null $oldEntity
     */
    protected function preSave($entity, $oldEntity=null)
    {
        /** @var $entity \Acts\CamdramBundle\Entity\Show */

        //ensure all the associated performances are also saved
        foreach ($entity->getPerformances() as $performance) {
            $performance->setShow($entity);
            $this->getDoctrine()->getManager()->persist($performance);
        }

        //ensure the venue attached to the show and to the performances are consistent
        $entity->updateVenues();
        //ensure the start_at and end_at fields are equal to the start and end of the first and last performances
        $entity->updateTimes();
    }

    protected function getEntity($identifier)
    {
        $show = parent::getEntity($identifier);
        //In order to simplify the interface, phasing out the 'excluding' field in performance date ranges. The method
        //below replaces any performance range with an 'excluding' field with two performance ranges.
        $show->fixPerformanceExcludes();
        return $show;
    }

    protected function getForm($show = null)
    {
        if (is_null($show)) {
            $show = new Show();
            $show->addPerformance(new Performance());
        }
        return $this->createForm(new ShowType(), $show);
    }
}
