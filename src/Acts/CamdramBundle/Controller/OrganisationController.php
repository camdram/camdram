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
use Acts\CamdramSecurityBundle\Entity\PendingAccess,
    Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents,
    Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent,
    Acts\CamdramSecurityBundle\Event\PendingAccessEvent,
    Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;

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

    protected abstract function getPerformances($slug, \DateTime $from, \DateTime $to);

    protected abstract function getShows($slug, \DateTime $from, \DateTime $to);

    /**
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     * @return mixed
     */
    public function getShowsAction(Request $request, $identifier)
    {
        if ($request->query->has('from')) {
            $from = new \DateTime($request->query->get('from'));
        }
        else {
            $from = $this->get('acts.time_service')->getCurrentTime();
        }

        if ($request->query->has('to')) {
            $to = new \DateTime($request->query->get('to'));
        }
        else {
            $to = clone $from;
            $to->modify('+1 year');
        }

        $shows = $this->getShows($identifier, $from, $to);
        return $this->view($shows, 200);
    }

    /**
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     * @return mixed
     */
    public function getEventsAction(Request $request, $identifier)
    {
        $diary = $this->get('acts.diary.factory')->createDiary();

        if ($request->query->has('from')) {
            $from = new \DateTime($request->query->get('from'));
        }
        else {
            $from = $this->get('acts.time_service')->getCurrentTime();
        }

        if ($request->query->has('to')) {
            $to = new \DateTime($request->query->get('to'));
        }
        else {
            $to = clone $from;
            $to->modify('+1 year');
        }

        $performances = $this->getPerformances($identifier, $from, $to);
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
        } else {
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
        } else {
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

    /**
     * Get a form for adding an admin to an organisation.
     *
     * @param $identifier
     */
    public function editAdminAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);
        if ($org->getEntityType() == 'society') {
            $route = 'post_society_admin';
        }
        else {
            $route = 'post_venue_admin';
        }

        $ace = new PendingAccess();
        $ace->setRid($org->getId());
        $ace->setType($org->getEntityType());
        $ace->setIssuer($this->getUser());
        $form = $this->createForm(new PendingAccessType(), $ace, array(
            'action' => $this->generateUrl($route, array('identifier' => $identifier))));

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($org);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($org);
        return $this->view($form, 200)
            ->setData(array(
                'entity' => $org, 
                'admins' => $admins,
                'pending_admins' => $pending_admins,
                'form' => $form->createView())
            )
            ->setTemplate('ActsCamdramSecurityBundle:PendingAccess:edit.html.twig');
    }

    /**
     * Create a new admin associated with this organisation.
     *
     * If the given email address isn't associated with an existing user, then
     * they will be given a pending access token, and invited via email to
     * create an account.
     *
     * An explicit ACE will be created if the user doesn't already have access
     * to the organisation by some other means, e.g. they have
     * suitable site-wide privileges.
     *
     * @param $identifier
     */
    public function postAdminAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $pending_ace = new PendingAccess();
        $form = $this->createForm(new PendingAccessType(), $pending_ace);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /* Check if the ACE doesn't need to be created for various reasons. */
            /* Is this person already an admin? */
            $already_admin = False;
            $admins = $this->get('acts.camdram.moderation_manager')
                        ->getModeratorsForEntity($org);
            foreach ($admins as $admin) {
                if ($admin->getEmail() == $pending_ace->getEmail()) {
                    $already_admin = True;
                    break;
                }
            }
            if ($already_admin == False) {
                /* If this person is already a Camdram user then grant access immediately. */
                $em = $this->getDoctrine()->getManager();
                $existing_user = $em->getRepository('ActsCamdramSecurityBundle:User')
                                    ->findOneByEmail($pending_ace->getEmail());
                if ($existing_user == null) {
                    /* Users with accounts created in v1 will have just their CRSid stored
                     * in the database, so check for that too.
                     */
                    $crs_id = ereg_replace("@cam.ac.uk", "", $pending_ace->getEmail());
                    $existing_user = $em->getRepository('ActsCamdramSecurityBundle:User')
                                        ->findOneByEmail($crs_id);
                }

                if ($existing_user != null) {
                    $this->get('camdram.security.acl.provider')
                        ->grantAccess($org, $existing_user, $this->getUser());
                } else {
                    /* This is an unknown email address. Check if they've already
                     * got a pending access token for this resource, otherwise
                     * create the pending access token.
                     */
                    $pending_repo = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess');
                    if ($pending_repo->isDuplicate($pending_ace) == False) {
                        $pending_ace->setIssuer($this->getUser());
                        $em->persist($pending_ace);
                        $em->flush();
                        $this->get('event_dispatcher')->dispatch(CamdramSecurityEvents::PENDING_ACCESS_CREATED, 
                            new PendingAccessEvent($pending_ace)); 
                    }
                }
            }
        }
        if ($org->getEntityType() == 'society') {
            $route = 'get_society';
        }
        else {
            $route = 'get_venue';
        }
        return $this->routeRedirectView($route, array('identifier' => $org->getSlug()));
    }

    /**
     * Revoke an admin's access to an organisation.
     */
    public function revokeAdminAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('uid');
        $user= $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($id);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->revokeAccess($org, $user, $this->getUser());
        }
        if ($org->getEntityType() == 'society') {
            $route = 'edit_society_admin';
        }
        else {
            $route = 'edit_venue_admin';
        }
        return $this->routeRedirectView($route, array('identifier' => $org->getSlug()));
    }

}

