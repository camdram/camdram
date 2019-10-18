<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;

class ApplicationController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
    }

    private function getApplicationForm(Show $show, $obj = null, $method = 'POST')
    {
        if (!$obj) {
            $obj = new Application();
            $obj->setShow($show);
        }
        $form = $this->createForm(ApplicationType::class, $obj, ['method' => $method]);

        return $form;
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/new")
     */
    public function newApplicationAction($identifier, Helper $helper)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/application-new.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/application")
     */
    public function postApplicationAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setData(array('show' => $show, 'form' => $form->createView()))
                ->setTemplate('show/application-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/application/edit")
     */
    public function editApplicationAction($identifier, Helper $helper)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application, 'PUT');

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('show/application-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/application")
     */
    public function putApplicationAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application, 'PUT');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->routeRedirectView('edit_show_application', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setData(array('show' => $show, 'form' => $form->createView()))
                ->setTemplate('show/application-edit.html.twig');
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
    public function expireApplicationAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

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
     * @Rest\Delete("/shows/{identifier}/application")
     * @return \FOS\RestBundle\View\View
     */
    public function deleteApplicationAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        return $this->routeRedirectView('new_show_application', array('identifier' => $show->getSlug()));
    }
}
