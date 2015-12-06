<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Entity\Audition;

/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction(Request $request)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate(new \DateTime());

        $week_manager = $this->get('acts.camdram.week_manager');
        $weeks = array();
        foreach ($auditions as $audition) {
            $weeks[$audition->getShow()->getId()] = $week_manager->getPerformancesWeeksAsString($audition->getShow()->getPerformances());
        }
        $view = $this->render(
            'ActsCamdramBundle:Audition:index.'.$request->getRequestFormat().'.twig',
            array('auditions' => $auditions,
                  'weeks' => $weeks)
            );

        return $view;
    }

    public function cgetDiaryAction()
    {
        $diary = $this->get('acts.diary.factory')->createDiary();

        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findUpcoming(null, new \DateTime());

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromAuditions($auditions);
        $diary->addEvents($events);

        $view = $this->view($diary)
            ->setTemplateVar('diary')
            ->setTemplate('ActsCamdramBundle:Audition:diary.html.twig');

        return $view;
    }

    public function getAction($identifier)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')
            ->findOneByShowSlug($identifier, new \DateTime());
        if ($auditions) {
            return $this->redirect($this->generateUrl('get_auditions').'#'.$auditions->getShow()->getSlug());
        } else {
            throw $this->createNotFoundException('No audition advert exists with that identifier');
        }
    }
}
