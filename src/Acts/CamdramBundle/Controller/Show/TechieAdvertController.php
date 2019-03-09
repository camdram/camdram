<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;

class TechieAdvertController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
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
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/new")
     */
    public function newTechieAdvertAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/techie-advert-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/techie-advert")
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

            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('show/techie-advert-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/edit")
     */
    public function editTechieAdvertAction(Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert, 'PUT');

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/techie-advert-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/techie-advert")
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

            return $this->routeRedirectView('edit_show_techie_advert', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('show/techie-advert-edit.html.twig');
        }
    }

    /**
     * @Rest\Patch("/shows/{identifier}/techie-advert/expire")
     *
     * @param Request $request
     * @param $identifier
     *
     * @return \FOS\RestBundle\View\View
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
        $techie_advert->setExpiry($now)->setDeadlineTime($now);
        $em->flush();

        return $this->routeRedirectView('edit_show_techie_advert', array('identifier' => $show->getSlug()));
    }

    /**
     * @param Request $request
     * @param $identifier
     *
     * @Rest\Delete("/shows/{identifier}/techie/advert")
     * @return \FOS\RestBundle\View\View
     */
    public function deleteTechieAdvertAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($techie_advert);
        $em->flush();

        return $this->routeRedirectView('new_show_techie_advert', array('identifier' => $show->getSlug()));
    }
}
