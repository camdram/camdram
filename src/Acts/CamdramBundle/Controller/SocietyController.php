<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;


/**
 * @RouteResource("Society")
 */
class SocietyController extends FOSRestController
{
    public function newAction()
    {
        $form = $this->getForm();
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:Society:new.html.twig');
    }

    public function postAction(Request $request)
    {
        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_society', array('slug' => $form->getData()->getShortName()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Society:new.html.twig');
        }
        return $this->render('ActsCamdramBundle:Society:new.html.twig', array('form' => $form));
    }

    public function editAction($slug)
    {
        $society = $this->getSociety($slug);
        $form = $this->getForm($society);
        return $this->view($form, 200)
            ->setTemplateVar('form')
            ->setTemplate('ActsCamdramBundle:Society:edit.html.twig');
    }

    public function putAction(Request $request, $slug)
    {
        $society = $this->getSociety($slug);
        $form = $this->getForm($society);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->routeRedirectView('get_society', array('slug' => $form->getData()->getShortName()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Society:edit.html.twig');
        }
        return $this->render('ActsCamdramBundle:Society:edit.html.twig', array('form' => $form));
    }

    public function removeAction($slug)
    {
        $society = $this->getSociety($slug);
        $em = $this->getDoctrine()->getManager();
        $em->remove($society);
        $em->flush();
        return $this->routeRedirectView('get_societies');
    }

    public function cgetAction()
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Society');
        $societies = $repo->findAllOrderedByCollegeName();

        $view = $this->view($societies, 200)
            ->setTemplate("ActsCamdramBundle:Society:index.html.twig")
            ->setTemplateVar('societies')
        ;
        
        return $view;
    }

    public function getAction($slug)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Society');
        $society = $repo->findOneByShortName($slug);
        if (!$society) {
        throw $this->createNotFoundException(
            'No society found with the name '.$society);
        }
        $view = $this->view($society, 200)
            ->setTemplate("ActsCamdramBundle:Society:show.html.twig")
            ->setTemplateVar('society')
        ;
        
        return $view;
    }

    protected function getSociety($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Society');
        $society = $repo->findOneBySlug($slug);

        if (!$society) {
            throw $this->createNotFoundException('That society does not exist');
        }

        return $society;
    }

    protected function getForm($society = null)
    {
        return $this->createForm(new SocietyType(), $society);
    }
}
