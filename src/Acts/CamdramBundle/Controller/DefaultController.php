<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');
        $news = $news_repo->getRecent(20);

        return $this->render('ActsCamdramBundle:Default:index.html.twig', array('news' => $news));
    }

    public function thisWeekAction()
    {
        $diary = $this->get('acts.diary');
        //$diary->addEvent($blah);
        return $diary;
    }

    public function nextWeekAction()
    {
        $diary = $this->get('acts.diary');
        //$diary->addEvent($blah);
        return $diary;
    }

    public function statisticsAction()
    {
        $now = new \DateTime;
        $day = $now->format('N');
        if ($day == 7) $day = 0;

        $interval = new \DateInterval('P'.$day.'DT'.$now->format('H\\Hi\\Ms\\S'));
        $start = $now->sub($interval);
        $end = clone $start;
        $end->add(new \DateInterval('P7D'));

        $perf_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance')->getNumberInDateRange($start, $end);
        $s_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->getNumberInDateRange($start, $end);
        $people_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Person')->getNumberInDateRange($start, $end);
        $v_num = $this->getDoctrine()->getRepository('ActsCamdramBundle:Venue')->getNumberInDateRange($start, $end);

        $response = $this->render('ActsCamdramBundle:Default:statistics.html.twig', array(
            'show_num' => $s_num,
            'performance_num' => $perf_num,
            'people_num' => $people_num,
            'venue_num' => $v_num,
        ));
        $response->setSharedMaxAge(4*3600);
        return $response;
    }

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
}