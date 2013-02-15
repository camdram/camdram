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

    public function diaryAction($id)
    {
        /** @var $diary \Acts\DiaryBundle\Diary\Diary */
        $diary = $this->get('acts.diary.factory')->createDiary();

        $time_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TimePeriod');
        $period = $time_repo->findOneById($id);
        $diary->setDateRange($period->getStartAt(), $period->getEndAt());

        $repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');
        $shows = $repo->findByTimePeriod($id);
        foreach($shows as $show) {
            foreach ($show->getPerformances() as $perf) {
                $event = new MultiDayEvent();
                $event->setName($show->getName());
                $event->setStartDate($perf->getStartDate());
                $event->setEndDate($perf->getEndDate());
                $event->setStartTime($perf->getTime());
                $event->setVenue($perf->getVenue());

                $event->setLink($this->generateUrl('get_show', array('identifier' => $show->getSlug())));
                if ($show->getVenue() && $perf->getVenue() == $show->getVenue()->getName()) {
                    $event->setVenueLink($this->generateUrl('get_venue', array('identifier' => $show->getVenue()->getSlug())));
                }

                $diary->addEvent($event);
            }
        }

        return $diary;
    }
}
