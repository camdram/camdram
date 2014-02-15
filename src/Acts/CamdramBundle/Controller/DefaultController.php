<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Event\MultiDayEvent;

/**
 * Class DefaultController
 *
 * Controlled for the home page. Most areas of the home page are split up into sub-actions which are triggered
 * individually from the main template.
 *
 * @package Acts\CamdramBundle\Controller
 */

class DefaultController extends Controller
{
    /**
     * The home page.
     *
     * Also returns the time periods used by the mini-diary
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');
        $news = $news_repo->getRecent(20);

        $now = $this->get('acts.time_service')->getCurrentTime();
        $start = clone $now;
        $start->modify('-4 weeks');
        $end = clone $now;
        $end->modify('+10 weeks');
        $week_manager = $this->get('acts.camdram.week_manager');
        $weeks = $week_manager->findBetween($start, $end);
        $current_week = $week_manager->findAt($now);

        return $this->render('ActsCamdramBundle:Default:index.html.twig', array(
            'news' => $news,
            'weeks' => $weeks,
            'current_week' => $current_week,
        ));
    }

    /**
     * Renders the statistics ("7 shows in the last week" etc...)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statisticsAction()
    {
        $now = $this->get('acts.time_service')->getCurrentTime();
        $day = $now->format('N');
        if ($day == 7) $day = 0;

        $interval = new \DateInterval('P'.$day.'DT'.$now->format('H\\Hi\\Ms\\S'));
        $start = $now->sub($interval);
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $perf_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance')->getNumberInDateRange($start, $end);
        $s_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getNumberInDateRange($start, $end);
        $people_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Person')->getNumberInDateRange($start, $end);
        $v_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Venue')->getNumberInDateRange($start, $end)
            + $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getNumberOfVenueNamesInDateRange($start, $end);

        $response = $this->render('ActsCamdramBundle:Default:statistics.html.twig', array(
            'show_num' => $s_num,
            'performance_num' => $perf_num,
            'people_num' => $people_num,
            'venue_num' => $v_num,
        ));
        $response->setSharedMaxAge(4*3600);
        return $response;
    }

    /**
     * Renders the vacancies summary on the home page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function vacanciesAction()
    {
        $auditions_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition');
        $techie_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
        $applications_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application');

        $response = $this->render('ActsCamdramBundle:Default:vacancies.html.twig', array(
            'auditions' => $auditions_repo->findUpcoming(3),
            'techie_ads' => $techie_repo->findLatest(3),
            'app_ads' => $applications_repo->findLatest(3),
        ));
        $response->setSharedMaxAge(60);
        return $response;

    }

    /**
     * Renders the "This time..." section on the home page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historicDataAction()
    {
        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $show_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $data = array();
        $now = $this->get('acts.time_service')->getCurrentTime();

        foreach (array(1, 2, 5) as $years) {
            $date = clone $now;
            $date->modify('-'.$years.' years');
            $period = $time_repo->findAt($date);
            if ($period) {
                $shows = $show_repo->findMostInterestingByTimePeriod($period, 5);
                if (count($shows) > 0) $data[$years] = $shows;
            }
        }

        $response = $this->render('ActsCamdramBundle:Default:historic-data.html.twig', array('data' => $data));
        $response->setSharedMaxAge(3600);
        return $response;
    }
}