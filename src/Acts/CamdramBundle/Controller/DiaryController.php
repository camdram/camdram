<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Acts\DiaryBundle\Event\MultiDayEvent;
use Symfony\Component\HttpFoundation\Request;


class DiaryController extends FOSRestController
{

    public function indexAction()
    {


        return $this->render("ActsCamdramBundle:Diary:index.html.twig")
        ;
    }

    public function toolbarAction()
    {
        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriodGroup');
        $years = $repo->getYears();
        $current_year = date('Y');
        $groups = $repo->getGroupsByYear($current_year);
        $now = $this->get('acts.camdram.time_service')->getCurrentTime();
        $current_group = $repo->getGroupAt($now);

        return $this->render('ActsCamdramBundle:Diary:toolbar.html.twig', array(
            'years' => $years,
            'current_year' => $current_year,
            'groups' => $groups,
            'current_group' => $current_group,
        ));
    }

    public function contentAction($direction = null, $last_date = null)
    {
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');

        if (is_null($direction)) {
            $now = $this->get('acts.camdram.time_service')->getCurrentTime();
            $periods = $periods_repo->getTimePeriodsAt($now, 5);
        }
        else {
            $last_date = new \DateTime($last_date);
            $last_date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

            if ($direction == 'next') {
                $periods = $periods_repo->getTimePeriodsAfter($last_date, 3);
            }
            elseif ($direction == 'previous') {
                $periods = $periods_repo->getTimePeriodsBefore($last_date, 1);
            }
        }



        return $this->render("ActsCamdramBundle:Diary:content.html.twig", array(
            'periods' => $periods
        ));
    }

    public function periodAction($id)
    {
        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        return $this->render('ActsCamdramBundle:Diary:period.html.twig', array('period' => $period));
    }

    public function diaryAction($id)
    {
        /** @var $diary \Acts\DiaryBundle\Diary\Diary */
        $diary = $this->get('acts.diary.factory')->createDiary();

        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        $diary->setDateRange($period->getStartAt(), $period->getEndAt());

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $shows = $repo->findByTimePeriod($period);

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromShows($shows);
        $diary->addEvents($events);


        return $diary;
    }
}
