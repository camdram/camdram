<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Audition;

use Doctrine\Common\Collections\Criteria;


/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction()
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate();

        $view = $this->view($auditions, 200)
                  ->setTemplate("ActsCamdramBundle:Audition:index.html.twig")
                  ->setTemplateVar('auditions')
        ;
        return $view;
    }

    public function cgetDiaryAction()
    {
        $diary = $this->get('acts.diary.factory')->createDiary();

        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate();

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromAuditions($auditions);
        $diary->addEvents($events);

        $view = $this->view($diary)
            ->setTemplateVar('diary')
            ->setTemplate('ActsCamdramBundle:Audition:diary.html.twig');
        return $view;
    }
}
