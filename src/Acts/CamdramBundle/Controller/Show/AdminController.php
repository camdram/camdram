<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdminController extends FOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')->findOneBy(array('slug' => $identifier));
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
        $form = $this->createForm(PendingAccessType::class, $ace, array(
            'action' => $this->generateUrl('post_show_admin', array('identifier' => $identifier))));

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($show);
        $requested_admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getRequestedShowAdmins($show);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($show);

        return $this->view($form, 200)
            ->setData(
                array(
                    'entity' => $show,
                    'admins' => $admins,
                    'requested_admins' => $requested_admins,
                    'pending_admins' => $pending_admins,
                    'form' => $form->createView())
            )
            ->setTemplate('pending_access/edit.html.twig');
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
        $form = $this->createForm(PendingAccessType::class, $pending_ace);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /* Check if the ACE doesn't need to be created for various reasons. */
            /* Is this person already an admin? */
            $already_admin = false;
            $admins = $this->get('acts.camdram.moderation_manager')
                ->getModeratorsForEntity($show);
            foreach ($admins as $admin) {
                if ($admin->getEmail() == $pending_ace->getEmail()) {
                    $already_admin = true;
                    break;
                }
            }
            if ($already_admin == false) {
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
                    if ($pending_repo->isDuplicate($pending_ace) == false) {
                        $pending_ace->setIssuer($this->getUser());
                        $em->persist($pending_ace);
                        $em->flush();
                        $this->get('event_dispatcher')->dispatch(
                            CamdramSecurityEvents::PENDING_ACCESS_CREATED,
                            new PendingAccessEvent($pending_ace)
                        );
                    }
                }
            }
        }

        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Request to be an admin associated with this show.
     *
     *
     * @param $identifier
     */
    public function requestAdminAction($identifier)
    {
        $this->get('camdram.security.acl.helper')->ensureGranted('ROLE_USER');

        $show = $this->getEntity($identifier);
        if ($this->get('camdram.security.acl.helper')->isGranted('EDIT', $show)) {
            // TODO add a no-action return code.
            return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
        } else {
            // Check if there's already a matching request.
            $em = $this->getDoctrine()->getManager();
            $ace_repo = $em->getRepository('ActsCamdramSecurityBundle:AccessControlEntry');
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $request = $ace_repo->findAceRequest($user, $show);
            if ($request != null) {
                // A pre-existing request exists. Don't create another one.
                return $this->routeRedirectView('get_show', array('identifier' => $show->getSlug()));
            }

            $ace = new AccessControlEntry();
            $ace->setUser($this->getUser())
                ->setEntityId($show->getId())
                ->setCreatedAt(new \DateTime())
                ->setType('request-show');
            $em->persist($ace);
            $em->flush();
            $this->get('event_dispatcher')->dispatch(
                CamdramSecurityEvents::ACE_CREATED,
                new AccessControlEntryEvent($ace)
            );

            return $this->render('show/access_requested.html.twig');
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
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($id);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->approveShowAccess($show, $user, $this->getUser());
        }

        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Revoke an admin's access to a show.
     * 
     */
    public function deleteAdminAction(Request $request, $identifier, $uid)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('delete_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($uid);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->revokeAccess($show, $user, $this->getUser());
        }

        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Revoke a pending admin's access to a show.
     * 
     * @Rest\Delete("/shows/{identifier}/pending-admins/{uid}")
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        $show = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('delete_pending_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $pending_admin = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findOneById($uid);
        if ($pending_admin != null) {
            $em->remove($pending_admin);
            $em->flush();
        }

        return $this->routeRedirectView('edit_show_admin', array('identifier' => $show->getSlug()));
    }
}
