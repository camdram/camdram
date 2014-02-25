<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramBundle\Form\Type\OrganisationApplicationType;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class OrganisationController
 *
 * Abstract controller for REST actions for organisations. Inherits from AbstractRestController.
 *
 */
abstract class OrganisationController extends AbstractRestController
{

    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Organisation $org)
    {
        $em = $this->getDoctrine()->getManager();
        $admins = $this->get('camdram.security.acl.provider')->getOwners($org);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($org);

        return $this->render(
            'ActsCamdramBundle:'.$this->getController().':admin-panel.html.twig',
            array('org' => $org,
                'admins' => $admins,
                'pending_admins' => $pending_admins)
        );
    }

    public function getNewsAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');

        return $this->view($news_repo->getRecentByOrganisation($org, 30), 200)
            ->setTemplateVar('news')
            ->setTemplate('ActsCamdramBundle:Organisation:news.html.twig')
            ;
    }

    /**
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     * @return mixed
     */
    public function getShowsAction($identifier)
    {
        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
        $now = $this->get('acts.time_service')->getCurrentTime();
        $performances = $performance_repo->getUpcomingBySociety($now, $this->getEntity($identifier));

        $diary = $this->get('acts.diary.factory')->createDiary();

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromPerformances($performances);
        $diary->addEvents($events);

        $view = $this->view($diary, 200)
            ->setTemplateVar('diary')
            ->setTemplate('ActsCamdramBundle:Organisation:shows.html.twig')
        ;

        return $view;
    }

    private function getApplicationForm(Organisation $org, $obj = null)
    {
        if (!$obj) {
            $obj = new Application();
            $obj->setSociety($org);
        }
        $form = $this->createForm(new OrganisationApplicationType(), $obj);
        return $form;
    }

    /**
     * @param $identifier
     */
    public function newApplicationAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getApplicationForm($org);
        return $this->view($form, 200)
            ->setData(array('org' => $org, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':application-new.html.twig');
    }

    /**
     * @param $identifier
     */
    public function postApplicationAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getApplicationForm($org);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, array('identifier' => $org->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':application-new.html.twig');
        }
    }

    /**
     * @param $identifier
     */
    public function editApplicationAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $application = $org->getApplications()->first();
        $form = $this->getApplicationForm($org, $application);
        return $this->view($form, 200)
            ->setData(array('org' => $org, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':application-edit.html.twig');
    }

    /**
     * @param $identifier
     */
    public function putApplicationAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $application = $org->getApplications()->first();
        $form = $this->getApplicationForm($org, $application);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_'.$this->type, array('identifier' => $org->getSlug()));
        }
        else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:'.$this->getController().':application-edit.html.twig');
        }
    }

    public function removeApplicationAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $org);
        $application = $org->getApplications()->first();

        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();
        return $this->routeRedirectView('get_'.$this->type, array('identifier' => $org->getSlug()));
    }
}
