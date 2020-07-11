<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Service\Time;
use Acts\CamdramBundle\Form\Type\AdvertType;
use Acts\DiaryBundle\Diary\Diary;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractFOSRestController
{
    /**
     * @Route("/vacancies.{_format}", format="html", methods={"GET"}, name="get_adverts")
     */
    public function cgetAction(Request $request)
    {
        $filter = $request->get('filter', null);
        if (!in_array($filter, [
            null, Advert::TYPE_ACTORS, Advert::TYPE_APPLICATION, Advert::TYPE_DESIGN, Advert::TYPE_OTHER, Advert::TYPE_TECHNICAL
        ])) {
            throw $this->createNotFoundException('Invalid filter');
        }

        $adverts = $this->getDoctrine()->getRepository(Advert::class)->findNotExpiredOrderedByDateName($filter, Time::now());

        switch ($request->getRequestFormat()) {
        case 'html':
        case 'txt':
            return $this->render("advert/index.{$request->getRequestFormat()}.twig",
                ['filter' => $filter, 'adverts' => $adverts]);
        default:
            return $this->view($adverts);
        }
    }

    /**
     * @Route("/vacancies/diary.{_format}", format="html", methods={"GET"}, name="get_adverts_diary")
     */
    public function cgetDiaryAction(Request $request)
    {
        $diary = new Diary;

        $auditions = $this->getDoctrine()->getRepository(Audition::class)->findUpcoming(null, Time::now());
        $diary->addEvents($auditions);

        if ($request->getRequestFormat() == 'html') {
            return $this->render('advert/diary.html.twig', ['filter' => Advert::TYPE_ACTORS, 'diary' => $diary]);
        } else {
            return $this->view($diary);
        }
    }

    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Advert $advert)
    {
        return $this->render(
            'advert/admin-panel.html.twig', [
                'advert' => $advert
            ]);
    }

    /**
     * Backwards compatibility
     * 
     * @Route("/vacancies/auditions.{_format}", format="html", methods={"GET"})
     */
    public function getAuditionsAction(Request $request)
    {
        return $this->redirectToRoute('get_adverts', [
            '_format' => $request->getRequestFormat(),
            'filter' => Advert::TYPE_ACTORS,
        ], 301);
    }

    /**
     * Backwards compatibility
     *
     * @Route("/vacancies/techies.{_format}", format="html", methods={"GET"})
     * @Route("/vacancies/techies/{identifier}", methods={"GET"})
     */
    public function getTechiesAction(Request $request)
    {
        return $this->redirectToRoute('get_adverts', [
            '_format' => $request->getRequestFormat(),
            'filter' => Advert::TYPE_TECHNICAL,
        ], 301);
    }

    /**
     * Backwards compatibility
     *
     * @Route("/vacancies/applications.{_format}", format="html", methods={"GET"})
     * @Route("/vacancies/applications/{identifier}", methods={"GET"})
     */
    public function getApplicationsAction(Request $request)
    {
        return $this->redirectToRoute('get_adverts', [
            '_format' => $request->getRequestFormat(),
            'filter' => Advert::TYPE_APPLICATION,
        ], 301);
    }

    /**
     * @Route("/vacancies/{identifier<\d+>}.{_format}", format="html", methods={"GET"}, name="get_advert")
     */
    public function getAction(Request $request, $identifier)
    {
        $advert = $this->getDoctrine()->getRepository(Advert::class)
                ->find($identifier);
        if (!$advert) throw $this->createNotFoundException('That advert does not exist.');
        $this->denyAccessUnlessGranted('VIEW', $advert);

        if ($request->getRequestFormat() == 'html') {
            $diary = new Diary;
            $diary->addEvents($advert->getAuditions());

            return $this->render('advert/view.html.twig', [
                'filter' => $advert->getType(),
                'diary' => $diary,
                'advert' => $advert,
            ]);
        } else {
            return $this->view($advert);
        }
    }

    // ----- Editing actions ----- //

    /**
     * @Route("/vacancies/{id<\d+>}/edit", methods={"GET"}, name="edit_advert")
     */
    public function editAdvert(Request $request, int $id)
    {
        $advert = $this->getDoctrine()->getRepository(Advert::class)->find($id);
        $this->denyAccessUnlessGranted('EDIT', $advert);

        $form = $this->createForm(AdvertType::class, $advert, ['method' => 'PUT']);

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/vacancies/{id<\d+>}", methods={"PUT"}, name="put_advert")
     */
    public function putAdvert(Request $request, int $id)
    {
        $advert = $this->getDoctrine()->getRepository(Advert::class)->find($id);
        $this->denyAccessUnlessGranted('EDIT', $advert);

        $form = $this->createForm(AdvertType::class, $advert, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('get_advert', ['identifier' => $advert->getId()]);
        } else {
            return $this->render('advert/edit.html.twig', [
                'advert' => $advert,
                'form' => $form->createView()
            ])->setStatusCode(400);
        }
    }

    /**
     * Finds entities and checks access-control etc.
     */
    private function getAdCheckEditable(Request $request, int $id, string $csrf_name): Advert
    {
        $advert = $this->getDoctrine()->getRepository(Advert::class)->find($id);
        if (!$advert) throw $this->createNotFoundException('That advert does not exist.');

        if (!$this->isCsrfTokenValid($csrf_name, $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $this->denyAccessUnlessGranted('EDIT', $advert);

        return $advert;
    }


    private function redirectToParentsAds(\Acts\CamdramBundle\Entity\BaseEntity $parent)
    {
        return $this->redirectToRoute([
            'show'    => 'get_show_adverts',
            'society' => 'acts_camdram_society_adverts',
            'venue'   => 'acts_camdram_venue_adverts',
        ][$parent->getEntityType()], ['identifier' => $parent->getSlug()]);
    }

    /**
     * @Route("/vacancies/{id<\d+>}/embedded", methods={"DELETE"}, name="delete_embedded_advert")
     */
    public function deleteEmbeddedAdvert(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'delete_advert');

        $em = $this->getDoctrine()->getManager();
        $em->remove($advert);
        $em->flush();

        return $this->redirectToParentsAds($advert->getParentEntity());
    }

    /**
     * @Route("/vacancies/{id<\d+>}", methods={"DELETE"}, name="delete_advert")
     */
    public function deleteAdvert(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'delete_advert');

        $em = $this->getDoctrine()->getManager();
        $em->remove($advert);
        $em->flush();

        return $this->redirectToRoute('get_adverts');
    }

    /**
     * @Route("/vacancies/{id<\d+>}/hide-embedded", methods={"PATCH"}, name="hide_embedded_advert")
     */
    public function hideEmbeddedAction(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'hide_advert');
        $advert->setDisplay(false);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToParentsAds($advert->getParentEntity());
    }

    /**
     * @Route("/vacancies/{id<\d+>}/hide", methods={"PATCH"}, name="hide_advert")
     */
    public function hideAction(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'hide_advert');
        $advert->setDisplay(false);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('get_advert', ['identifier' => $advert->getId()]);
    }

    /**
     * @Route("/vacancies/{id<\d+>}/show-embedded", methods={"PATCH"}, name="show_embedded_advert")
     */
    public function showEmbeddedAction(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'show_advert');
        $advert->setDisplay(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToParentsAds($advert->getParentEntity());
    }

    /**
     * @Route("/vacancies/{id<\d+>}/show", methods={"PATCH"}, name="show_advert")
     */
    public function showAction(Request $request, int $id): Response
    {
        $advert = $this->getAdCheckEditable($request, $id, 'show_advert');
        $advert->setDisplay(true);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('get_advert', ['identifier' => $advert->getId()]);
    }

}
