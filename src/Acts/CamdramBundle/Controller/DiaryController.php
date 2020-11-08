<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Service\WeekManager;
use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Diary\Label;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class DiaryController
 *
 * Controller for the diary page. The diary
 */
class DiaryController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Renders the main diary template
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, WeekManager $week_manager)
    {
        $now = Time::now();
        $week_start = $week_manager->previousSunday($now);

        return $this->dateAction($request, $week_manager, $week_start);
    }

    /**
     * Sub-action which renders the toolbar which allows the user to switch term/year
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toolbarAction($start_date = null)
    {
        if (!$start_date) {
            $start_date = Time::now();
        }
        $current_year = $start_date->format('Y');

        $repo = $this->getDoctrine()->getRepository(Entity\Performance::class);
        $first_date = $repo->getFirstDate();
        $last_date = $repo->getLastDate();
        $years = range($first_date->format('Y'), $last_date->format('Y'));

        $repo = $this->getDoctrine()->getRepository(Entity\TimePeriod::class);
        $periods = $repo->findByYearBefore($current_year, $last_date);
        $current_period = $repo->findAt($start_date);

        return $this->render('diary/toolbar.html.twig', array(
            'years' => $years,
            'current_year' => $current_year,
            'periods' => $periods,
            'current_period' => $current_period,
        ));
    }

    private function renderDiary(Request $request, Diary $diary)
    {
        if ($request->getRequestFormat() != 'html') {
            return $this->view($diary);
        } else if ($request->get('fragment') || $request->isXmlHttpRequest()) {
            return $this->render('diary/fragment.html.twig', ['diary' => $diary]);
        } else {
            return $this->render('diary/index.html.twig', ['diary' => $diary]);
        }
    }

    public function yearAction(Request $request, WeekManager $week_manager, $year)
    {
        $start_date = \DateTime::createFromFormat('!Y', $year);

        return $this->dateAction($request, $week_manager, $start_date);
    }

    public function periodAction(Request $request, WeekManager $week_manager, $year, $period)
    {
        $period = $this->getDoctrine()->getRepository(Entity\TimePeriod::class)->getBySlugAndYear($period, $year);
        if ($period) {
            return $this->dateAction($request, $week_manager, $period->getStartAt());
        } else {
            throw $this->createNotFoundException('Invalid time period specified');
        }
    }

    public function weekAction(Request $request, WeekManager $week_manager, $year, $period, $week)
    {
        $week = $this->getDoctrine()->getRepository(Entity\WeekName::class)->getByYearPeriodAndSlug($year, $period, $week);
        if ($week) {
            return $this->dateAction($request, $week_manager, $week->getStartAt());
        } else {
            throw $this->createNotFoundException('Invalid week specified');
        }
    }

    /**
     * Renders a single week.
     *
     * @param string $date Start date of the week to be rendered
     */
    public function singleWeekAction(Request $request, WeekManager $week_manager, $date)
    {
        $start_date = $week_manager->previousSunday(\DateTime::createFromFormat('!Y-m-d', $date));
        $end_date = clone $start_date;
        $end_date->modify('+1 week');
        $diary = new Diary;
        $diary->setDateRange($start_date, $end_date);

        $this->populateDiary($diary, $start_date, $end_date);

        return $this->renderDiary($request, $diary);
    }

    public function dateAction(Request $request, WeekManager $week_manager, $start)
    {
        if (is_string($start)) {
            $start = \DateTime::createFromFormat('!Y-m-d', $start);
            if (!$start) {
                throw new BadRequestHttpException('Invalid start date', null, 400);
            }
        }

        if ($request->query->has('end')) {
            $end = \DateTime::createFromFormat('!Y-m-d', $request->query->get('end'));
            if (!$end) {
                throw new BadRequestHttpException('Invalid end date', null, 400);
            }
        } else {
            $end = clone $start;
            $end->modify('+8 weeks');
        }

        $diary = new Diary;

        $this->populateDiary($diary, $start, $end);

        $repo = $this->getDoctrine()->getRepository(Entity\WeekName::class);
        foreach ($repo->findBetween($start, $end) as $name) {
            $diary->addLabel(Label::TYPE_WEEK, $name->getShortName(), $name->getStartAt());
        }
        $repo = $this->getDoctrine()->getRepository(Entity\TimePeriod::class);
        foreach ($repo->findIntersecting($start, $end) as $period) {
            $diary->addLabel(Label::TYPE_PERIOD, $period->getFullName(), $period->getStartAt(), $period->getEndAt());
        }

        $diary->setDateRange($week_manager->previousSunday($start), $week_manager->nextSunday($end));

        return $this->renderDiary($request, $diary);
    }

    private function populateDiary(Diary $diary, \DateTime $from, \DateTime $to)
    {
        $performances = $this->em->getRepository(Entity\Performance::class)
                           ->findInDateRange($from, $to);
        $diary->addEvents($performances);

        $events = $this->em->createQuery(
            'SELECT e, x FROM ' . Entity\Event::class . ' e ' .
            'LEFT JOIN e.link_id x ' .
            'WHERE e.start_at > :from AND e.start_at < :to')
            ->setParameters(compact('from', 'to'))->getResult();
        $diary->addEvents($events);
    }
}
