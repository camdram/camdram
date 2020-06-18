<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Service\Time;
use Acts\DiaryBundle\Diary\Diary;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuditionController extends AbstractFOSRestController
{
    /**
     * @Route("/vacancies/auditions.{_format}", format="html", methods={"GET"}, name="get_auditions")
     */
    public function cgetAction(Request $request)
    {
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findCurrentOrderedByNameDate(Time::now());

        switch ($request->getRequestFormat()) {
        case 'html':
        case 'txt':
            return $this->render("audition/index.{$request->getRequestFormat()}.twig",
                ['auditions' => $auditions]);
        default:
            return $this->view($auditions);
        }
    }

    /**
     * @Route("/vacancies/auditions/diary.{_format}", format="html", methods={"GET"}, name="get_auditions_diary")
     */
    public function cgetDiaryAction()
    {
        $diary = new Diary;

        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findUpcoming(null, Time::now());
        $diary->addEvents($auditions);

        return $this->show('audition/diary.html.twig', 'diary', $diary);
    }

    /**
     * @Route("/vacancies/auditions/{identifier}.{_format}", format="html", methods={"GET"}, name="get_audition")
     */
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
