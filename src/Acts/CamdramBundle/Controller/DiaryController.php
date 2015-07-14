<?php

namespace Acts\CamdramBundle\Controller;

use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Diary\Label;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * Class DiaryController
 *
 * Controller for the diary page. The diary
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
        $now = $this->get('acts.time_service')->getCurrentTime();
        $week_start = $this->get('acts.camdram.week_manager')->previousSunday($now);

        return $this->dateAction($week_start);
    }

    /**
     * Sub-action which renders the toolbar which allows the user to switch term/year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toolbarAction($start_date = null)
    {
        if (!$start_date) {
            $start_date = $this->get('acts.time_service')->getCurrentTime();
        }
        $current_year = $start_date->format('Y');

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $first_date = $repo->getFirstShowDate();
        $last_date = $repo->getLastShowDate();
        $years = range($first_date->format('Y'), $last_date->format('Y'));

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $periods = $repo->findByYearBefore($current_year, $last_date);
        $current_period = $repo->findAt($start_date);

        return $this->render('ActsCamdramBundle:Diary:toolbar.html.twig', array(
            'years' => $years,
            'current_year' => $current_year,
            'periods' => $periods,
            'current_period' => $current_period,
        ));
    }

    private function renderDiary(Diary $diary)
    {
        $view = $this->view($diary)
            ->setTemplateVar('diary');
        if ($this->getRequest()->get('fragment') || $this->getRequest()->isXmlHttpRequest()) {
            $view->setTemplate('ActsCamdramBundle:Diary:fragment.html.twig');
        } else {
            $view->setTemplate('ActsCamdramBundle:Diary:index.html.twig');
        }

        return $view;
    }

    public function yearAction($year)
    {
        $start_date = new \DateTime($year.'-01-01');

        return $this->dateAction($start_date);
    }

    public function periodAction($year, $period)
    {
        $period = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod')->getBySlugAndYear($period, $year);
        if ($period) {
            return $this->dateAction($period->getStartAt());
        } else {
            throw $this->createNotFoundException('Invalid time period specified');
        }
    }

    public function weekAction($year, $period, $week)
    {
        if (preg_match('/[0-9]{4}\-[0-9]{2}\-[0-9]{2}/', $week)) {
            return $this->rangeAction($week);
        } elseif (preg_match('/[0-9]{2}\-[0-9]{2}/', $week)) {
            return $this->rangeAction($year.'-'.$week);
        }

        $week = $this->getDoctrine()->getRepository('ActsCamdramBundle:WeekName')->getByYearPeriodAndSlug($year, $period, $week);
        if ($week) {
            return $this->dateAction($week->getStartAt());
        } else {
            throw $this->createNotFoundException('Invalid week specified');
        }
    }

    /**
     * Renders a single week.
     *
     * @param $date \DateTime Start date of the week to be rendered
     *
     * @return \Acts\DiaryBundle\Diary\Diary
     */
    public function singleWeekAction($date)
    {
        $week_manager = $this->get('acts.camdram.week_manager');
        $start_date = $week_manager->previousSunday(new \DateTime($date));
        $end_date = clone $start_date;
        $end_date->modify('+1 week');
        $diary = $this->get('acts.diary.factory')->createDiary();
        $diary->setDateRange($start_date, $end_date);

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
        $performances = $repo->findInDateRange($start_date, $end_date);

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromPerformances($performances);
        $diary->addEvents($events);

        return $this->renderDiary($diary);
    }

    public function dateAction($start)
    {
        if (is_string($start)) {
            $start = new \DateTime($start);
        }

        if ($this->getRequest()->query->has('end')) {
            $end = new \DateTime($this->getRequest()->query->get('end'));
        } elseif ($this->getRequest()->query->has('length')) {
            $end = clone $start;
            $end->modify($this->getRequest()->query->get('length'));
        } else {
            $end = clone $start;
            $end->modify('+8 weeks');
        }

        $diary = $this->get('acts.diary.factory')->createDiary();

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
        $performances = $repo->findInDateRange($start, $end);
        $events = $this->get('acts.camdram.diary_helper')->createEventsFromPerformances($performances);
        $diary->addEvents($events);

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:WeekName');
        foreach ($repo->findBetween($start, $end) as $name) {
            $diary->addLabel(Label::TYPE_WEEK, $name->getShortName(), $name->getStartAt());
        }
        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        foreach ($repo->findIntersecting($start, $end) as $period) {
            $diary->addLabel(Label::TYPE_PERIOD, $period->getFullName(), $period->getStartAt(), $period->getEndAt());
        }

        $week_manager = $this->get('acts.camdram.week_manager');
        $diary->setDateRange($week_manager->previousSunday($start), $week_manager->nextSunday($end));

        return $this->renderDiary($diary);
    }
}
