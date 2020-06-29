<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TimePeriodController
 *
 */
class TimePeriodController extends AbstractFOSRestController
{
    /**
     * @Route("/time-periods/{year}.{_format}", methods={"GET"}, name="get_time-period")
     */
    public function getAction($year)
    {
        $final_date = $this->getDoctrine()->getRepository(\Acts\CamdramBundle\Entity\Performance::class)->getLastDate();
        $periods = $this->getDoctrine()->getRepository(\Acts\CamdramBundle\Entity\TimePeriod::class)
                      ->findByYearBefore($year, $final_date);

        return $this->view($periods, 200);
    }
}
