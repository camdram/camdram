<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application,
    Acts\CamdramBundle\Entity\Person,
    Acts\CamdramBundle\Entity\Role,
    Acts\CamdramBundle\Entity\TechieAdvert;
use Acts\CamdramBundle\Form\Type\ApplicationType;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Acts\CamdramBundle\Form\Type\TechieAdvertType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Performance;
use Acts\CamdramBundle\Form\Type\RoleType;
use Acts\CamdramBundle\Form\Type\ShowType;
use Acts\CamdramSecurityBundle\Entity\PendingAccess,
    Acts\CamdramSecurityBundle\Entity\AccessControlEntry,
    Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents,
    Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent,
    Acts\CamdramSecurityBundle\Event\PendingAccessEvent,
    Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

use Gedmo\Sluggable\Util as Sluggable;

/**
 * Class ShowController
 *
 * Controller for REST actions for shows. Inherits from AbstractRestController.
 * @RouteResource("Show")
 */
class ShowController extends AbstractRestController
{

    protected $class = 'Acts\\CamdramBundle\\Entity\\Show';

    protected $type = 'show';

    protected $type_plural = 'shows';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
    }

    protected function getEntity($identifier)
    {
        $show = parent::getEntity($identifier);
        //In order to simplify the interface, phasing out the 'excluding' field in performance date ranges. The method
        //below replaces any performance range with an 'excluding' field with two performance ranges.
        $show->fixPerformanceExcludes();
        return $show;
    }

    protected function getForm($show = null)
    {
        if (is_null($show)) {
            $show = new Show();
            $show->addPerformance(new Performance());
        }
        return $this->createForm(new ShowType($this->get('security.context')), $show);
    }

    public function cgetAction(Request $request)
    {
        if ($request->getRequestFormat() == 'rss') {
            $now = $this->get('acts.time_service')->getCurrentTime();
            $next_week = clone $now;
            $next_week->modify('+10 days');
            $shows = $this->getRepository()->findInDateRange($now, $next_week);
            return $this->view($shows);
        }
        else {
            return parent::cgetAction($request);
        }
    }

    private function getTechieAdvertForm(Show $show, $obj = null)
    {
        if (!$obj) {
            $obj = new TechieAdvert();
            $obj->setShow($show);
        }
        $form = $this->createForm(new TechieAdvertType(), $obj);
        return $form;
    }

    /**
     * Render the Admin Panel
     */
    public function adminPanelAction(Show $show)
    {
        $em = $this->getDoctrine()->getManager();
        $admins = $this->get('camdram.security.acl.provider')->getOwners($show);
        $requested_admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getRequestedShowAdmins($show);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($show);
        if ($show->getSociety()) $admins[] = $show->getSociety();
        if ($show->getVenue()) $admins[] = $show->getVenue();

        return $this->render(
            'ActsCamdramBundle:Show:admin-panel.html.twig',
            array('show' => $show,
                  'admins' => $admins,
                  'requested_admins' => $requested_admins,
                  'pending_admins' => $pending_admins)
            );
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/new")
     */
    public function newTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
    }

    public function approveAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('APPROVE', $show);
        $this->get('acts.camdram.moderation_manager')->approveEntity($show);
        $this->get('doctrine.orm.entity_manager')->flush();
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * @param $identifier
     * @Rest\Post("/shows/{identifier}/techie-advert")
     */
    public function postTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getTechieAdvertForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-new.html.twig');
        }
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/techie-advert/edit")
     */
    public function editTechieAdvertAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/techie-advert")
     */
    public function putTechieAdvertAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $techie_advert = $show->getTechieAdverts()->first();
        $form = $this->getTechieAdvertForm($show, $techie_advert);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('edit_show_techie_advert', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:techie-advert-edit.html.twig');
        }
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

    private function getAuditionsForm(Show $show)
    {
        return $this->createForm(new ShowAuditionsType(), $show);
    }

    /**
     * @param $identifier
     * @Rest\Get("/shows/{identifier}/auditions/edit")
     */
    public function editAuditionsAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);
        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:auditions-edit.html.twig');
    }

    /**
     * @param $identifier
     * @Rest\Put("/shows/{identifier}/auditions")
     */
    public function putAuditionsAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $form = $this->getAuditionsForm($show);
        $form->submit($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate('ActsCamdramBundle:Show:auditions-edit.html.twig');
        }
    }

    public function getRolesAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        return $this->getAction($identifier);
    }

    /**
     * Get a form for adding an admin to a show.
     *
     * @param $identifier
     */
    public function editAdminAction($identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $ace = new PendingAccess();
        $ace->setRid($show->getId());
        $ace->setType('show');
        $ace->setIssuer($this->getUser());
        $form = $this->createForm(new PendingAccessType(), $ace, array(
            'action' => $this->generateUrl('post_show_admin', array('identifier' => $identifier))));

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($show);
        $requested_admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getRequestedShowAdmins($show);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($show);
        return $this->view($form, 200)
            ->setData(array(
                'entity' => $show, 
                'admins' => $admins,
                'requested_admins' => $requested_admins,
                'pending_admins' => $pending_admins,
                'form' => $form->createView())
            )
            ->setTemplate('ActsCamdramSecurityBundle:PendingAccess:edit.html.twig');
    }

    /**
     * Create a new admin associated with this show.
     *
     * If the given email address isn't associated with an existing user, then
     * they will be given a pending access token, and invited via email to
     * create an account.
     *
     * An explicit ACE will be created if the user doesn't already have access
     * to the show by some other means, e.g. they are an admin for the show's
     * funding society, the venue the show is being performed at, or have
     * suitable site-wide privileges.
     *
     * @param $identifier
     */
    public function postAdminAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $pending_ace = new PendingAccess();
        $form = $this->createForm(new PendingAccessType(), $pending_ace);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /* Check if the ACE doesn't need to be created for various reasons. */
            /* Is this person already an admin? */
            $already_admin = False;
            $admins = $this->get('acts.camdram.moderation_manager')
                        ->getModeratorsForEntity($show);
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
                if ($existing_user != null) {
                    $this->get('camdram.security.acl.provider')
                        ->grantAccess($show, $existing_user, $this->getUser());
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
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Request to be an admin associated with this show.
     *
     *
     * @param $identifier
     */
    public function requestAdminAction($identifier)
    {
        $show = $this->getEntity($identifier);
        if ($this->get('camdram.security.acl.helper')->isGranted('EDIT', $show)) {
            // TODO add a no-action return code.
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            $em = $this->getDoctrine()->getManager();
            $ace = new AccessControlEntry();
            $ace->setUser($this->getUser())
                ->setEntityId($show->getId())
                ->setCreatedAt(new \DateTime)
                ->setType('request-show');
            $em->persist($ace);
            $em->flush();
            $this->get('event_dispatcher')->dispatch(CamdramSecurityEvents::ACE_CREATED, 
                new AccessControlEntryEvent($ace)); 
            return $this->render("ActsCamdramBundle:Show:access_requested.html.twig");
        }
    }

    /**
     * Approve a request to be an admin for this show.
     *
     * @param $identifier
     */
    public function approveAdminAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('uid');
        $user= $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($id);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->approveShowAccess($show, $user, $this->getUser());
        }
        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Revoke an admin's access to a show.
     */
    public function revokeAdminAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('uid');
        $user= $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($id);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->revokeAccess($show, $user, $this->getUser());
        }
        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Get a form for adding a single role to a show.
     *
     * @param $identifier
     */
    public function newRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show, false);

        $role = new Role();
        $role->setType($request->query->get('type'));
        $form = $this->createForm(new RoleType(), $role, array(
            'action' => $this->generateUrl('post_show_role', array('identifier' => $identifier))));

        return $this->view($form, 200)
            ->setData(array('show' => $show, 'form' => $form->createView()))
            ->setTemplate('ActsCamdramBundle:Show:role-new.html.twig');
    }

    /**
     * Create a new role associated with this show.
     *
     * Creates a new person if they're not already part of Camdram.
     * @param $identifier
     */
    public function postRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        $base_role = new Role();
        $form = $this->createForm(new RoleType(), $base_role);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            /* Try and find the person. TODO slug will be unique, but not the best
             * way for searching. E.g. john-smith, john-smith1 may both be slugs.
             * A better approach may be to do a case-insensitive search by name and
             * choose the most recent record. Such behaviour is more than what the
             * existing codebase does.
             */
            $names = explode(",", $form->get('name')->getData());
            foreach ($names as $name) {
                $role = clone $base_role;
                $name = trim($name);
                $slug = Sluggable\Urlizer::urlize($name, '-');
                $person_repo = $em->getRepository('ActsCamdramBundle:Person');
                $person = $person_repo->findOneBySlug($slug);
                if ($person == null) {
                    $person = New Person();
                    $person->setName($name);
                    $person->setSlug($slug);
                    $em->persist($person);
                }
                $role->setPerson($person);
                $order = $this->getDoctrine()->getRepository('ActsCamdramBundle:Role')
                            ->getMaxOrderByShowType($show, $role->getType());
                $role->setOrder(++$order);
                $role->setShow($show);
                $em->persist($role);

                $person->addRole($role);
                $show->addRole($role);
                $em->flush();
            }
        }
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }

    /**
     * Remove a role from a show.
     */
    public function removeRoleAction(Request $request, $identifier)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('role');
        $role_repo = $em->getRepository('ActsCamdramBundle:Role');
        $role = $role_repo->findOneById($id);
        if ($role != null) {
            $person = $role->getPerson();
            $show->removeRole($role);
            $role_repo->removeRoleFromOrder($role);
            $em->remove($role);
            $em->flush();
            // Ensure the person is not an orphan.
            if ($person->getRoles()->isEmpty()) {
                $em->remove($person);
                $em->flush();
            }
        }
        return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
    }
}
