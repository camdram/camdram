<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Acts\DiaryBundle\Event\MultiDayEvent;
use Symfony\Component\HttpFoundation\Request;


class DiaryController extends FOSRestController
{

    public function indexAction()
    {
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $periods = $periods_repo->getCurrentTimePeriods(5);

        $view = $this->view($periods, 200)
            ->setTemplate("ActsCamdramBundle:Diary:index.html.twig")
            ->setTemplateVar('diary')
        ;
        return $view;
    }

    public function periodAction($id)
    {
        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        return $this->render('ActsCamdramBundle:Diary:period.html.twig', array('period' => $period));
    }

    public function periodDiaryAction($id)
    {
        /** @var $diary \Acts\DiaryBundle\Diary\Diary */
        $diary = $this->get('acts.diary.factory')->createDiary();

        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        $diary->setDateRange($period->getStartAt(), $period->getEndAt());

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $shows = $repo->findByTimePeriod($id);

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromShows($shows);
        $diary->addEvents($events);


        return $diary;
    }
}
