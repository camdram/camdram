<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\ShowType;
use Symfony\Component\HttpFoundation\Request;


/**
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

    protected function preSave($entity, $oldEntity=null)
    {
        /** @var $entity \Acts\CamdramBundle\Entity\Show */

        foreach ($entity->getPerformances() as $performance) {
            $performance->setShow($entity);
            $this->getDoctrine()->getManager()->persist($performance);
        }

        $entity->updateVenues();
        $entity->updateTimes();
    }

    protected function getEntity($identifier)
    {
        $show = parent::getEntity($identifier);
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
