<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Service\Time;
use Acts\DiaryBundle\Diary\Diary;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractFOSRestController
{
    /**
     * @Route("/vacancies/auditions.{_format}", format="html", methods={"GET"}, name="get_adverts")
     */
    public function cgetAction(Request $request)
    {
        $adverts = $this->getDoctrine()->getRepository(Advert::class)->findNotExpiredOrderedByDateName(Time::now());

        switch ($request->getRequestFormat()) {
        case 'html':
        case 'txt':
            return $this->render("advert/index.{$request->getRequestFormat()}.twig",
                ['adverts' => $adverts]);
        default:
            return $this->view($adverts);
        }
    }

    /**
     * @Route("/vacancies/auditions/diary.{_format}", format="html", methods={"GET"}, name="get_adverts_diary")
     */
    public function cgetDiaryAction(Request $request)
    {
        $diary = new Diary;

        $auditions = $this->getDoctrine()->getRepository(Audition::class)->findUpcoming(null, Time::now());
        $diary->addEvents($auditions);

        if ($request->getRequestFormat() == 'html') {
            return $this->render('advert/diary.html.twig', ['diary' => $diary]);
        } else {
            return $this->view($diary);
        }
    }

    /**
     * @Route("/vacancies/auditions/{identifier}.{_format}", format="html", methods={"GET"}, name="get_advert")
     */
    public function getAction(Request $request, $identifier)
    {
        $advert = $this->getDoctrine()->getRepository(Advert::class)
                ->findOneById($identifier, Time::now());
        if (!$advert) {
            throw $this->createNotFoundException('No advert exists with that identifier');
        }

        if ($request->getRequestFormat() == 'html') {
            return $this->render('advert/view.html.twig', ['advert' => $advert]);
        } else {
            return $this->view($advert);
        }
    }
}
