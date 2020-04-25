<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Acts\CamdramBundle\Service\WeekManager;
use Acts\DiaryBundle\Diary\Diary;

/**
 * Class DefaultController
 *
 * Controlled for the home page. Most areas of the home page are split up into sub-actions which are triggered
 * individually from the main template.
 */
class DefaultController extends AbstractController
{
    /**
     * The home page.
     *
     * Also returns the time periods used by the mini-diary
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(WeekManager $week_manager)
    {
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');
        $news = $news_repo->getRecent(20);

        $now = new \DateTime;
        $start = clone $now;
        $start->modify('-4 weeks');
        $end = clone $now;
        $end->modify('+10 weeks');
        $weeks = $week_manager->findBetween($start, $end);
        $current_week = $week_manager->findAt($now);

        return $this->render('home/index.html.twig', array(
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
        $now = new \Datetime;
        $day = $now->format('N');
        if ($day == 7) {
            $day = 0;
        }

        $interval = new \DateInterval('P'.$day.'DT'.$now->format('H\\Hi\\Ms\\S'));
        $start = $now->sub($interval);
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $perf_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance')->getNumberInDateRange($start, $end);
        $s_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getNumberInDateRange($start, $end);
        $people_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Person')->getNumberInDateRange($start, $end);
        $v_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Venue')->getNumberInDateRange($start, $end)
            + $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance')->getNumberOfVenueNamesInDateRange($start, $end);

        $response = $this->render('home/statistics.html.twig', array(
            'show_num' => $s_num,
            'performance_num' => $perf_num,
            'people_num' => $people_num,
            'venue_num' => $v_num,
        ));
        $response->setSharedMaxAge(4 * 3600);

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
        $now = new \DateTime();

        $response = $this->render('home/vacancies.html.twig', array(
            'auditions' => $auditions_repo->findUpcoming(3, $now),
            'techie_ads' => $techie_repo->findLatest(3, $now),
            'app_ads' => $applications_repo->findLatest(3, $now),
        ));
        $response->setSharedMaxAge(60);

        return $response;
    }

    /**
     * Renders the "This time..." section on the home page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historicDataAction(WeekManager $week_manager)
    {
        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $show_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $data = array();
        $now = new \DateTime;

        foreach (array(1, 2, 5) as $years) {
            $date = clone $now;
            $date->modify('-'.$years.' years');
            $week = $week_manager->findAt($date);
            if ($week) {
                $shows = $show_repo->findMostInterestingByWeek($week, 5);
                if (count($shows) > 0) {
                    $data[$years] = $shows;
                }
            }
        }

        $response = $this->render('home/historic-data.html.twig', array('data' => $data));
        $response->setSharedMaxAge(3600);

        return $response;
    }
}
