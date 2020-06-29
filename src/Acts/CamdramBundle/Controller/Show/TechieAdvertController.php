<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TechieAdvertController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository(Show::class)->findOneBy(array('slug' => $identifier));
    }

    private function getTechieAdvertForm(Show $show, $obj = null, $method = 'POST')
    {
        if (!$obj) {
            $obj = new TechieAdvert();
            $obj->setShow($show);
        }
        $form = $this->createForm(TechieAdvertType::class, $obj, ['method' => $method]);

        return $form;
    }

    /**
     * @Route("/shows/{identifier}/techie-advert/new", methods={"GET"}, name="new_show_techie_advert")
     */
    public function newTechieAdvertAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);

        return $this->render('show/techie-advert-new.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * @Route("/shows/{identifier}/techie-advert", methods={"POST"}, name="post_show_techie_advert")
     */
    public function postTechieAdvertAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->render('show/techie-advert-new.html.twig',
                ['show' => $show, 'form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * @Route("/shows/{identifier}/techie-advert/edit", methods={"GET"}, name="edit_show_techie_advert")
     */
    public function editTechieAdvertAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert, 'PUT');

        return $this->render('show/techie-advert-edit.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * @Route("/shows/{identifier}/techie-advert", methods={"PUT"}, name="put_show_techie_advert")
     */
    public function putTechieAdvertAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert, 'PUT');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('edit_show_techie_advert', array('identifier' => $show->getSlug()));
        } else {
            return $this->render('show/techie-advert-edit.html.twig',
                ['show' => $show, 'form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * @Route("/shows/{identifier}/techie-advert/expire", methods={"PATCH"}, name="expire_show_techie_advert")
     */
    public function expireTechieAdvertAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        /** @var TechieAdvert $techie_advert */
        $techie_advert = $show->getTechieAdverts()->first();
        $em = $this->getDoctrine()->getManager();

        $now = new \DateTime;
        $techie_advert->setDeadline(true);
        $techie_advert->setExpiry($now);
        $em->flush();

        return $this->redirectToRoute('edit_show_techie_advert', array('identifier' => $show->getSlug()));
    }

    /**
     * @Route("/shows/{identifier}/techie/advert", methods={"DELETE"}, name="delete_show_techie_advert")
     */
    public function deleteTechieAdvertAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($techie_advert);
        $em->flush();

        return $this->redirectToRoute('new_show_techie_advert', array('identifier' => $show->getSlug()));
    }
}
