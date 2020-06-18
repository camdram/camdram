<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @Route("/shows/{identifier}/application/new", methods={"GET"}, name="new_show_application")
     */
    public function newApplicationAction($identifier, Helper $helper)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $form = $this->getApplicationForm($show);

        return $this->render('show/application-new.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * @Route("/shows/{identifier}/application", methods={"POST"}, name="post_show_application")
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

            return $this->redirectToRoute('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->render('show/application-new.html.twig',
                ['show' => $show, 'form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * @Route("/shows/{identifier}/application/edit", methods={"GET"}, name="edit_show_application")
     */
    public function editApplicationAction($identifier, Helper $helper)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $form = $this->getApplicationForm($show, $application, 'PUT');

        return $this->render('show/application-edit.html.twig',
            ['show' => $show, 'form' => $form->createView()]);
    }

    /**
     * @Route("/shows/{identifier}/application", methods={"PUT"}, name="put_show_application")
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

            return $this->redirectToRoute('edit_show_application', array('identifier' => $show->getSlug()));
        } else {
            return $this->render('show/application-edit.html.twig',
                ['show' => $show, 'form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * @Route("/shows/{identifier}/application/expire", methods={"PATCH"}, name="expire_show_application")
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

        return $this->redirectToRoute('edit_show_application', array('identifier' => $show->getSlug()));
    }

    /**
     * @Route("/shows/{identifier}/application", methods={"DELETE"}, name="delete_show_application")
     */
    public function deleteApplicationAction(Request $request, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $application = $show->getApplications()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        return $this->redirectToRoute('new_show_application', array('identifier' => $show->getSlug()));
    }
}
