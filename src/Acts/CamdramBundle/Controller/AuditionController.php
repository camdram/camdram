<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;

use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Entity\Audition;
use Acts\DiaryBundle\Diary\Diary;

/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction(Request $request)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate(Time::now());

        $view = $this->view($auditions, 200)
                  ->setTemplate('audition/index.'.$request->getRequestFormat().'.twig')
                   ->setTemplateVar('auditions')
               ;

        return $view;
    }

    public function cgetDiaryAction()
    {
        $diary = new Diary;

        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findUpcoming(null, Time::now());
        $diary->addEvents($auditions);

        $view = $this->view($diary)
            ->setTemplateVar('diary')
            ->setTemplate('audition/diary.html.twig');

        return $view;
    }

    public function getAction($identifier)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')
            ->findOneByShowSlug($identifier, Time::now());
        if ($auditions) {
            return $this->redirect($this->generateUrl('get_auditions').'#'.$auditions->getShow()->getSlug());
        } else {
            throw $this->createNotFoundException('No audition advert exists with that identifier');
        }
    }
}
