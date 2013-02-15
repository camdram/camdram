<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ShowType;
use Symfony\Component\HttpFoundation\Request;


class DiaryController extends FOSRestController
{

    public function indexAction()
    {
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $periods = $periods_repo->getCurrentTimePeriods(7);

        $view = $this->view($periods, 200)
            ->setTemplate("ActsCamdramBundle:Diary:index.html.twig")
            ->setTemplateVar('diary')
        ;
        return $view;
    }

    public function periodAction($id)
    {
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $periods_repo->findOneById($id);

        if (!$period) {
            throw $this->createNotFoundException('Invalid time period id');
        }

        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
        $performances = $performance_repo->findPerformancesByPeriod($period->getStartAt(), $period->getEndAt());

        $view = $this->view($performances, 200)
            ->setTemplate("ActsCamdramBundle:Diary:period.html.twig")
            ->setTemplateVar('period')
        ;
        return $view;
    }
}
