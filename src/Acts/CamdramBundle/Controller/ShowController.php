<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ShowType;
use Symfony\Component\HttpFoundation\Request;


/**
 * @RouteResource("Show")
 */
class ShowController extends FOSRestController
{

    public function newAction()
    {
        $form = $this->getForm();
        return $this->render('ActsCamdramBundle:Show:new.html.twig', array('form' => $form->createView()));
    }

    public function postAction(Request $request)
    {
        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('show')
                ->setTemplate('ActsCamdramBundle:Show:new.html.twig');
        }
    }

    public function editAction($slug)
    {
        $show = $this->getShow($slug);
        $form = $this->getForm($show);
        return $this->render('ActsCamdramBundle:Show:edit.html.twig', array('form' => $form->createView()));
    }

    public function putAction(Request $request, $slug)
    {
        $show = $this->getShow($slug);
        $form = $this->getForm($show);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView('get_show', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('show')
                ->setTemplate('ActsCamdramBundle:Show:edit.html.twig');
        }
    }

    public function removeAction($slug)
    {
        $show = $this->getShow($slug);
        $em = $this->getDoctrine()->getManager();
        $em->remove($show);
        $em->flush();
        return $this->routeRedirectView('get_shows');
    }

    public function getAction($slug)
    {
        $show = $this->getShow($slug);
        
        $view = $this->view($show, 200)
            ->setTemplate("ActsCamdramBundle:Show:show.html.twig")
            ->setTemplateVar('show')
        ;
        
        return $view;
    }

    public function cgetAction()
    {
        return $this->render('ActsCamdramBundle:Show:index.html.twig');
    }

    protected function getShow($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
        $show = $repo->findOneBySlug($slug);

        if (!$show) {
            throw $this->createNotFoundException('That show does not exist');
        }

        return $show;
    }

    protected function getForm($show = null)
    {
        return $this->createForm(new ShowType(), $show);
    }
}
