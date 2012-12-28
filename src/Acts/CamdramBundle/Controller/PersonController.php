<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Acts\CamdramBundle\Entity\Person;
use Acts\CamdramBundle\Form\Type\PersonType;


/**
 * @RouteResource("Person")
 */
class PersonController extends FOSRestController
{
    public function newAction()
    {
        $form = $this->getForm();
        return $this->render('ActsCamdramBundle:Person:new.html.twig', array('form' => $form->createView()));
    }

    public function postAction(Request $request)
    {
        $form = $this->getForm();
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_person', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('person')
                ->setTemplate('ActsCamdramBundle:Person:new.html.twig');
        }
    }

    public function editAction($slug)
    {
        $person = $this->getPerson($slug);
        $form = $this->getForm($person);
        return $this->render('ActsCamdramBundle:Person:edit.html.twig', array('form' => $form->createView()));
    }

    public function putAction(Request $request, $slug)
    {
        $person = $this->getPerson($slug);
        $form = $this->getForm($person);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->routeRedirectView('get_person', array('slug' => $form->getData()->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('person')
                ->setTemplate('ActsCamdramBundle:Person:edit.html.twig');
        }
    }

    public function removeAction($slug)
    {
        $person = $this->getPerson($slug);
        $em = $this->getDoctrine()->getManager();
        $em->remove($person);
        $em->flush();
        return $this->routeRedirectView('get_persons');
    }

    public function getAction($slug)
    {
        $person = $this->getPerson($slug);

        $view = $this->view($person, 200)
            ->setTemplate("ActsCamdramBundle:Person:show.html.twig")
            ->setTemplateVar('person')
        ;
        
        return $view;
    }

    public function cgetAction()
    {
        return $this->render('ActsCamdramBundle:Person:index.html.twig');
    }

    protected function getPerson($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Person');
        $person = $repo->findOneBySlug($slug);

        if (!$person) {
            throw $this->createNotFoundException('That person does not exist');
        }

        return $person;
    }

    protected function getForm($person = null)
    {
        return $this->createForm(new PersonType(), $person);
    }
}
