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
    public function indexAction($year, $period)
    {
        if (($query_year = $this->getRequest()->query->get('year')) && ($query_period = $this->getRequest()->query->get('period'))) {
            return $this->redirect($this->generateUrl('acts_camdram_diary_select', array(
                'year' => $query_year,
                'period' => $query_period,
            )));
        }

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriodGroup');

        if (!$year || !$period) {
            $current_year = $this->get('acts.time_service')->getCurrentTime()->format('Y');
            $group = $repo->getGroupAt($this->get('acts.time_service')->getCurrentTime());
        }
        else {
            $current_year = $year;
            $group = $repo->findOneByYearAndSlug($year, $period);
        }

        if ($group->getStartAt()->format('Y') != $current_year) {
            return $this->redirect($this->generateUrl('acts_camdram_diary_select', array(
                'year' => $group->getStartAt()->format('Y'),
                'period' => $period,
            )));
        }

        $years = $repo->getYears();

        $final_date = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getLastShowDate();
        $groups = $repo->findByYearBefore($current_year, $final_date);


        return $this->render("ActsCamdramBundle:Diary:index.html.twig", array(
            'years' => $years,
            'current_year' => $current_year,
            'groups' => $groups,
            'current_group' => $group,
            'provided_year' => $year,
            'provided_period' => $period,
        ));
    }

    /**
     * Sub-action which renders the toolbar which allows the user to switch term/year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toolbarAction()
    {


        return $this->render('ActsCamdramBundle:Diary:toolbar.html.twig', array(

        ));
    }

    public function contentAction($year, $period)
    {
        $groups_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriodGroup');
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $limit = $this->getRequest()->query->get('limit', 5);

        if ($year && $period) {
            $group = $groups_repo->findOneByYearAndSlug($year, $period);
            $periods = $periods = $periods_repo->getTimePeriodsAt($group->getStartAt(), $limit);
        }
        else {
            $now = $this->get('acts.time_service')->getCurrentTime();
            $periods = $periods = $periods_repo->getTimePeriodsAt($now, $limit);
        }

        return $this->render("ActsCamdramBundle:Diary:content.html.twig", array(
            'periods' => $periods
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
    public function relativeAction($direction = null, $last_date = null)
    {
        $periods_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $shows_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');

        $last_date = new \DateTime($last_date);
        $last_date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        if ($direction == 'next') {
            $final_date = $shows_repo->getLastShowDate();
            $limit = $this->getRequest()->get('limit', 3);
            $periods = $periods_repo->findBetween($last_date, $final_date, $limit);
        }
        elseif ($direction == 'previous') {
            $limit = $this->getRequest()->get('limit', 1);
            $periods = $periods_repo->findBefore($last_date, $limit);
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
