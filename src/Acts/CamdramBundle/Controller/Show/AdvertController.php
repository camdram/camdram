<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Advert;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\AdvertType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdvertController extends AbstractFOSRestController
{
    protected function getShow($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBySlug($identifier);
    }

    protected function getEntity($identifier, $advertId)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Advert')->findOneByShowAndId($identifier, $advertId);
    }

    /**
     * @Route("/shows/{identifier}/adverts", methods={"GET"}, name="get_show_adverts")
     */
    public function cgetAction(Request $request, $identifier)
    {
        $show = $this->getShow($identifier);

        return $this->render('show/adverts.html.twig', [
            'show' => $show,
        ]);
    }

    /**
     * @Route("/shows/{identifier}/adverts/new", methods={"GET"}, name="new_show_advert")
     */
    public function newAction(Request $request, $identifier)
    {
        $show = $this->getShow($identifier);
        $this->denyAccessUnlessGranted('EDIT', $show);

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
    public function postAction(Request $request, $identifier)
    {
        $show = $this->getShow($identifier);
        $this->denyAccessUnlessGranted('EDIT', $show);

        $advert = new Advert();
        $advert->setShow($show);
        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->redirectToRoute('get_show_adverts', ['identifier' => $show->getSlug()]);
        } else {
            return $this->render('show/advert-new.html.twig', [
                'show' => $show,
                'form' => $form->createView(),
            ]);
        }
    }

    /**
     * @Route("/shows/{identifier}/adverts/{advertId}/hide", methods={"PATCH"}, name="hide_show_advert")
     */
    public function hideAction(Request $request, $identifier, $advertId)
    {
        $advert = $this->getEntity($identifier, $advertId);
        $this->denyAccessUnlessGranted('EDIT', $advert->getShow());

        $advert->setDisplay(false);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('get_show_adverts', ['identifier' => $advert->getShow()->getSlug()]);
    }

    /**
     * @Route("/shows/{identifier}/adverts/{advertId}/show", methods={"PATCH"}, name="show_show_advert")
     */
    public function showAction(Request $request, $identifier, $advertId)
    {
        $advert = $this->getEntity($identifier, $advertId);
        $this->denyAccessUnlessGranted('EDIT', $advert->getShow());

        $advert->setDisplay(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirectToRoute('get_show_adverts', ['identifier' => $advert->getShow()->getSlug()]);
    }

    /**
     * @param $identifier
     * @Route("/shows/{identifier}/adverts/{advertId}/edit", methods={"GET"}, name="edit_show_advert")
     */
    public function editAction(Helper $helper, $identifier, $advertId)
    {
        $advert = $this->getEntity($identifier, $advertId);
        $this->denyAccessUnlessGranted('EDIT', $advert->getShow());
        $form = $this->createForm(AdvertType::class, $advert, ['method' => 'PUT']);

        return $this->render('show/advert-edit.html.twig', [
            'advert' => $advert, 
            'form' => $form->createView()
        ]);
    }

    /**
     * @param $identifier
     * @Route("/shows/{identifier}/adverts/{advertId}", methods={"PUT"}, name="put_show_advert")
     */
    public function putAuditionsAction(Request $request, $identifier, $advertId)
    {
        $advert = $this->getEntity($identifier, $advertId);
        $this->denyAccessUnlessGranted('EDIT', $advert->getShow());

        $form = $this->createForm(AdvertType::class, $advert, ['method' => 'PUT']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('get_show_adverts', array('identifier' => $advert->getShow()->getSlug()));
        } else {
            return $this->render('show/advert-edit.html.twig', [
                'advert' => $advert, 
                'form' => $form->createView()
            ])->setStatusCode(400);
        }
    }
}
