<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\AdvertType;
use Acts\CamdramBundle\EventListener\AdvertListener;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractController
{
    private function getAndCheckShow($identifier): Show
    {
        $show = $this->getDoctrine()->getRepository(Show::class)->findOneBySlug($identifier);
        if ($show == null) throw new NotFoundHttpException("That show does not exist.");
        $this->denyAccessUnlessGranted('EDIT', $show);
        return $show;
    }

    private function getAndCheckAdvert($identifier, $advertId): Advert
    {
        $show = $this->getAndCheckShow($identifier);
        $advert = $this->getDoctrine()->getRepository(Advert::class)->findOneBy(['show' => $show, 'id' => $advertId]);
        if (!$advert) throw new NotFoundHttpException("That advert does not exist.");
        return $advert;
    }

    /**
     * @Route("/shows/{identifier}/adverts", methods={"GET"}, name="get_show_adverts")
     */
    public function cgetAction(Request $request, $identifier)
    {
        return $this->render('show/adverts.html.twig', [
            'show' => $this->getAndCheckShow($identifier),
        ]);
    }

    /**
     * @Route("/shows/{identifier}/adverts/new", methods={"GET"}, name="new_show_advert")
     */
    public function newAction(Request $request, $identifier)
    {
        $show = $this->getAndCheckShow($identifier);

        $advert = new Advert();
        $advert->setShow($show);
        $form = $this->createForm(AdvertType::class, $advert);

        return $this->render('show/advert-new.html.twig', [
            'show' => $show,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/shows/{identifier}/adverts", methods={"POST"}, name="post_show_advert")
     */
    public function postAction(Request $request, $identifier, AdvertListener $listener)
    {
        $show = $this->getAndCheckShow($identifier);

        $advert = new Advert();
        $advert->setShow($show);
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $listener->updatePositions($advert);
            $em->flush();
            return $this->redirectToRoute('get_show_adverts', ['identifier' => $show->getSlug()]);
        } else {
            return $this->render('show/advert-new.html.twig', [
                'show' => $show,
                'form' => $form->createView(),
            ]);
        }
    }
}
