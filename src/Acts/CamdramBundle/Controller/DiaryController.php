<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Acts\DiaryBundle\Event\MultiDayEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DiaryController
 *
 * Controller for the diary page. The diary
 *
 * @package Acts\CamdramBundle\Controller
 */

class DiaryController extends FOSRestController
{

    /**
     * Renders the main diary template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {


        return $this->render("ActsCamdramBundle:Diary:index.html.twig")
        ;
    }

    /**
     * Sub-action which renders the toolbar which allows the user to switch term/year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * Sub-action which actually renders the diary. Also called by the AJAX callback which loads new data
     *
     * If both $direction and $last_date are null then the current time period and the 5 following time periods are
     * returned. If $direction is 'next', then the 3 time periods prior to $last_date are returned. If $direction is
     * $previous then the time period prior to $last_date is returned.
     *
     * @param null|string $direction
     * @param null|string $last_date
     * @return \Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * Renders a single time period. Called multiple times by contentAction's template.
     *
     * @param $id Primary key of the time period to be rendered
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function periodAction($id)
    {
        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        return $this->render('ActsCamdramBundle:Diary:period.html.twig', array('period' => $period));
    }

    /**
     * Renders the actually diary. It creates a Diary object, which is picked up by a response listener and
     * rendered by the DiaryBundle, which deals with the logic of working out which events to put in which row.
     *
     * @param $id Primary key of the time period to be rendered
     * @return \Acts\DiaryBundle\Diary\Diary
     */
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
