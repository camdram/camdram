<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class TimePeriodController
 * @package Acts\CamdramBundle\Controller
 * @RouteResource("time-period")
 */
class TimePeriodController extends FOSRestController
{
    public function getAction($year)
    {
        $periods = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriodGroup')
                      ->getGroupsByYear($year);
        $view = $this->view($periods, 200)
            ->setTemplateVar('periods')
        ;
        
        return $view;
    }
}
