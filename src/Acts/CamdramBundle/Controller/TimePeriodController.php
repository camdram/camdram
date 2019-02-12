<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

/**
 * Class TimePeriodController
 *
 * @RouteResource("time-period")
 */
class TimePeriodController extends FOSRestController
{
    public function getAction($year)
    {
        $final_date = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance')->getLastDate();
        $periods = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod')
                      ->findByYearBefore($year, $final_date);

        $view = $this->view($periods, 200)
            ->setTemplateVar('periods')
        ;

        return $view;
    }
}
