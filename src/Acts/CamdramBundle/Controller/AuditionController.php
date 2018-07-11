<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Entity\Audition;
use Acts\DiaryBundle\Diary\Diary;

/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction(Request $request)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate(new \DateTime());

        $view = $this->view($auditions, 200)
                  ->setTemplate('audition/index.'.$request->getRequestFormat().'.twig')
                   ->setTemplateVar('auditions')
               ;

        return $view;
    }

    public function cgetDiaryAction()
    {
        $diary = new Diary;

        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findUpcoming(null, new \DateTime());

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromAuditions($auditions);
        $diary->addEvents($events);

        $view = $this->view($diary)
            ->setTemplateVar('diary')
            ->setTemplate('audition/diary.html.twig');

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
