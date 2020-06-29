<?php

namespace Acts\CamdramBundle\Controller\Show;

use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\AccessControlEntry;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\AccessControlEntryEvent;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use Acts\CamdramSecurityBundle\Security\Acl\AclProvider;
use Acts\CamdramSecurityBundle\Security\Acl\Helper;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdminController extends AbstractFOSRestController
{
    protected function getEntity($identifier)
    {
        return $this->getDoctrine()->getRepository(\Acts\CamdramBundle\Entity\Show::class)->findOneBy(['slug' => $identifier]);
    }

    /**
     * Get a form for adding an admin to a show.
     *
     * @Rest\Get("/shows/{identifier}/admin/edit", name="edit_show_admin")
     */
    public function editAdminAction($identifier, Helper $helper)
    {
        return $this->createEditAdminResponse($identifier, null, $helper);
    }

    /**
     * This method's contents were formerly included in editAdminAction
     * directly. Splitting it off allows an alternative form to be passed.
     */
    private function createEditAdminResponse(string $identifier, $form, Helper $helper)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if ($form == null) {
            $ace = new PendingAccess();
            $ace->setRid($show->getId());
            $ace->setType('show');
            $ace->setIssuer($this->getUser());
            $form = $this->createForm(PendingAccessType::class, $ace, array(
                'action' => $this->generateUrl('post_show_admin', array('identifier' => $identifier))));
        }

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository(User::class)->getEntityOwners($show);
        $requested_admins = $em->getRepository(User::class)->getRequestedShowAdmins($show);
        $pending_admins = $em->getRepository(PendingAccess::class)->findByResource($show);

        return $this->render('pending_access/edit.html.twig', [
            'entity' => $show,
            'admins' => $admins,
            'requested_admins' => $requested_admins,
            'pending_admins' => $pending_admins,
            'form' => $form->createView()]);
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
     * @Rest\Post("/shows/{identifier}/admins", name="post_show_admin")
     */
    public function postAdminAction(Request $request, AclProvider $aclProvider, Helper $helper,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        $pending_ace = new PendingAccess();
        $form = $this->createForm(PendingAccessType::class, $pending_ace);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            // Form not valid, return it to user.
            return $this->createEditAdminResponse($show->getSlug(), $form, $helper);
        }
        /* Check if the ACE doesn't need to be created for various reasons. */

        /* Is this person already an admin? */
        /* Changed to only check for explicit admins of the show. */
        $admins = $aclProvider->getOwners($show);
        foreach ($admins as $admin) {
            if ($admin->getEmail() == $pending_ace->getEmail()) {
                $this->addFlash('error', "The user {$admin->getName()} " .
                   "<{$admin->getEmail()}> is already an admin so they weren't added again.");
                return $this->createEditAdminResponse($show->getSlug(), $form, $helper);
            }
        }

        /* If this person is already a Camdram user then grant access immediately. */
        $em = $this->getDoctrine()->getManager();
        $existing_user = $em->getRepository(User::class)
            ->findOneByEmail($pending_ace->getEmail());

        if ($existing_user != null) {
            $aclProvider->grantAccess($show, $existing_user, $this->getUser());
        } else {
            /* This is an unknown email address. Check if they've already
             * got a pending access token for this resource, otherwise
             * create the pending access token.
             */
            $pending_repo = $em->getRepository(PendingAccess::class);
            if ($pending_repo->isDuplicate($pending_ace) == false) {
                $pending_ace->setIssuer($this->getUser());
                $em->persist($pending_ace);
                $em->flush();
                $event_dispatcher->dispatch(
                    new PendingAccessEvent($pending_ace),
                    CamdramSecurityEvents::PENDING_ACCESS_CREATED
                );
            }
        }
        // Success
        return $this->redirectToRoute('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Request to be an admin associated with this show.
     *
     * @Rest\Post("/shows/{identifier}/admin/request", name="request_show_admin")
     */
    public function requestAdminAction(Request $request, Helper $helper, EventDispatcherInterface $event_dispatcher, $identifier)
    {
        $helper->ensureGranted('ROLE_USER');
        if (!$this->isCsrfTokenValid('show_request_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $show = $this->getEntity($identifier);
        if ($helper->isGranted('EDIT', $show)) {
            $this->addFlash('error', 'Cannot request admin rights on this show: you are already an admin.');
            return $this->redirectToRoute('get_show', ['identifier' => $show->getSlug()]);
        } else {
            // Check if there's already a matching request.
            $em = $this->getDoctrine()->getManager();
            $ace_repo = $em->getRepository(AccessControlEntry::class);
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $request = $ace_repo->findAceRequest($user, $show);
            if ($request != null) {
                // A pre-existing request exists. Don't create another one.
                $date = $request->getCreatedAt()->format('j F Y');
                $this->addFlash('error', "Can't request admin rights again; you put in a request on $date which has not yet been answered.");
                return $this->redirectToRoute('get_show', array('identifier' => $show->getSlug()));
            }

            $ace = new AccessControlEntry();
            $ace->setUser($this->getUser())
                ->setEntityId($show->getId())
                ->setCreatedAt(new \DateTime())
                ->setType('request-show');
            $em->persist($ace);
            $em->flush();
            $event_dispatcher->dispatch(
                new AccessControlEntryEvent($ace),
                CamdramSecurityEvents::ACE_CREATED
            );

            $this->addFlash('success', 'Your request for access to this show has been sent.');
            return $this->redirectToRoute('get_show', ['identifier' => $show->getSlug()]);
        }
    }

    /**
     * Approve a request to be an admin for this show.
     *
     * @Rest\Patch("/shows/{identifier}/admin/approve", name="approve_show_admin")
     */
    public function approveAdminAction(Request $request, AclProvider $aclProvider, Helper $helper, $identifier)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('approve_show_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('uid');
        $user = $em->getRepository(User::class)->findOneById($id);
        if ($user != null) {
            $aclProvider->approveShowAccess($show, $user, $this->getUser());
        } else {
            $this->addFlash('error', "Cannot approve access: user ID not found. Try again or contact support.");
        }

        return $this->redirectToRoute('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Revoke an admin's access to a show.
     *
     * @Rest\Delete("/shows/{identifier}/admins/{uid}", name="delete_show_admin")
     */
    public function deleteAdminAction(Request $request, AclProvider $aclProvider, Helper $helper, $identifier, $uid)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('delete_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneById($uid);
        if ($user != null) {
            $aclProvider->revokeAccess($show, $user, $this->getUser());
        } else {
            $this->addFlash('error', "Cannot delete admin: user ID not found. Try again or contact support.");
        }

        return $this->redirectToRoute('edit_show_admin', array('identifier' => $show->getSlug()));
    }

    /**
     * Revoke a pending admin's access to a show.
     *
     * @Rest\Delete("/shows/{identifier}/pending-admins/{uid}", name="delete_show_pending_admin")
     */
    public function deletePendingAdminAction(Request $request, Helper $helper, $identifier, $uid)
    {
        $show = $this->getEntity($identifier);
        $helper->ensureGranted('EDIT', $show);

        if (!$this->isCsrfTokenValid('delete_pending_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $pending_admin = $em->getRepository(PendingAccess::class)->findOneById($uid);
        if ($pending_admin != null) {
            $em->remove($pending_admin);
            $em->flush();
        } else {
            $this->addFlash('error', "Cannot delete pending admin: ID not found. Try again or contact support.");
        }

        return $this->redirectToRoute('edit_show_admin', array('identifier' => $show->getSlug()));
    }
}
