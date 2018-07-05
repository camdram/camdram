<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ApplicationController extends FOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
    }

    private function getApplicationForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new Application();
            $obj->setShow($show);
        }
        $form = $this->createForm(new ApplicationType(), $obj);

        return $form;
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/new")
     */
    public function newApplicationAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:application-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/application")
     */
    public function postApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:application-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/edit")
     */
    public function editApplicationAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application);

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:application-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/application")
     */
    public function putApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->routeRedirectView('edit_show_application', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:application-edit.html.twig');
        }
    }

    /**
     * @Rest\Patch("/shows/{identifier}/application/expire")
     *
     * @param Request $request
     * @param $identifier
     *
     * @return \FOS\RestBundle\View\View
     */
    public function expireApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        /** @var Application $application */
        $application = $show->getApplications()->first();
        $em = $this->getDoctrine()->getManager();

        $now = new \DateTime;
        $application->setDeadlineDate($now)->setDeadlineTime($now);
        $em->flush();

        return $this->routeRedirectView('edit_show_application', array('identifier' => $show->getSlug()));
    }

    /**
     * @param Request $request
     * @param $identifier
     *
     * @return \FOS\RestBundle\View\View
     */
    public function deleteApplicationAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        return $this->routeRedirectView('new_show_application', array('identifier' => $show->getSlug()));
    }
}
