<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
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

    protected function getForm($show = null)
    {
        return $this->createForm(new ShowType(), $show);
    }
}
