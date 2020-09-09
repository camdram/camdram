<?php

namespace Acts\CamdramBundle\Controller;

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * General AdminController for all OwnableInterfaces.
 */
class AdminController extends AbstractController
{
    protected function getEntity($type, $identifier)
    {
        $class = [
            'events'    => \Acts\CamdramBundle\Entity\Event::class,
            'shows'     => \Acts\CamdramBundle\Entity\Show::class,
            'societies' => \Acts\CamdramBundle\Entity\Society::class,
            'venues'    => \Acts\CamdramBundle\Entity\Venue::class
        ][$type];
        if ($type == 'events') return $this->getDoctrine()->getRepository($class)->find($identifier);
        return $this->getDoctrine()->getRepository($class)->findOneBy(['slug' => $identifier]);
    }

    /**
     * Get a form for adding an admin to a show.
     *
     * @Route("/{type<events|shows|societies|venues>}/{identifier}/admin/edit", name="edit_entity_admin", methods={"GET"})
     */
    public function editAdminAction(string $type, string $identifier, Helper $helper)
    {
        return $this->createEditAdminResponse($type, $identifier, null, $helper);
    }

    /**
     * This method's contents were formerly included in editAdminAction
     * directly. Splitting it off allows an alternative form to be passed.
     */
    private function createEditAdminResponse(string $type, string $identifier, $form, Helper $helper)
    {
        $entity = $this->getEntity($type, $identifier);
        $helper->ensureGranted('EDIT', $entity);

        if ($form == null) {
            $form = $this->createForm(PendingAccessType::class, new PendingAccess(), [
                'action' => $this->generateUrl('post_entity_admin', compact('type', 'identifier'))
            ])->createView();
        }

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository(User::class)->getEntityOwners($entity);
        $pending_admins = $em->getRepository(PendingAccess::class)->findByResource($entity);
        $requested_admins = $entity instanceof \Acts\CamdramBundle\Entity\Show ?
            $em->getRepository(User::class)->getRequestedShowAdmins($entity) : null;

        return $this->render('pending_access/edit.html.twig', compact(
            'entity', 'admins', 'requested_admins', 'pending_admins','form'));
    }

    /**
     * Create a new admin associated with this entity.
     *
     * If the given email address isn't associated with an existing user, then
     * they will be given a pending access token, and invited via email to
     * create an account.
     *
     * An explicit ACE will be created if the user doesn't already have access
     * to the show.
     *
     * @Route("/{type<events|shows|societies|venues>}/{identifier}/admins", methods={"POST"}, name="post_entity_admin")
     */
    public function postAdminAction(Request $request, AclProvider $aclProvider, Helper $helper,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher,
        $type, $identifier)
    {
        $entity = $this->getEntity($type, $identifier);
        $helper->ensureGranted('EDIT', $entity);

        $pending_ace = new PendingAccess();
        $pending_ace->setRid($entity->getId());
        $pending_ace->setType($entity->getEntityType());
        $pending_ace->setIssuer($this->getUser());
        $form = $this->createForm(PendingAccessType::class, $pending_ace);
        $form->handleRequest($request);
        if (!$form->isValid()) {
            // Form not valid, return it to user.
            return $this->createEditAdminResponse($type, $identifier, $form, $helper);
        }
        /* Check if the ACE doesn't need to be created for various reasons. */

        /* Is this person already an admin? */
        /* Changed to only check for explicit admins of shows. */
        $admins = $aclProvider->getOwners($entity);
        foreach ($admins as $admin) {
            if ($admin->getEmail() == $pending_ace->getEmail()) {
                $this->addFlash('error', "The user {$admin->getName()} " .
                   "<{$admin->getEmail()}> is already an admin so they weren't added again.");
                return $this->createEditAdminResponse($type, $identifier, $form, $helper);
            }
        }

        /* If this person is already a Camdram user then grant access immediately. */
        $em = $this->getDoctrine()->getManager();
        $existing_user = $em->getRepository(User::class)
            ->findOneByEmail($pending_ace->getEmail());

        if ($existing_user != null) {
            $aclProvider->grantAccess($entity, $existing_user, $this->getUser());
        } else {
            /* This is an unknown email address. Check if they've already
             * got a pending access token for this resource, otherwise
             * create the pending access token.
             */
            $pending_repo = $em->getRepository(PendingAccess::class);
            if ($pending_repo->isDuplicate($pending_ace) == false) {
                $em->persist($pending_ace);
                $em->flush();
                $event_dispatcher->dispatch(
                    new PendingAccessEvent($pending_ace),
                    CamdramSecurityEvents::PENDING_ACCESS_CREATED
                );
            }
        }
        // Success
        return $this->redirectToRoute('edit_entity_admin', compact('type', 'identifier'));
    }

    /**
     * Request to be an admin associated with this show. Only available for shows.
     *
     * @Route("/shows/{identifier}/admin/request", methods={"POST"}, name="request_show_admin")
     */
    public function requestAdminAction(Request $request, Helper $helper, EventDispatcherInterface $event_dispatcher, $identifier)
    {
        $helper->ensureGranted('ROLE_USER');
        if (!$this->isCsrfTokenValid('show_request_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $show = $this->getEntity('shows', $identifier);
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
     * @Route("/shows/{identifier}/admin/approve", methods={"PATCH"}, name="approve_show_admin")
     */
    public function approveAdminAction(Request $request, AclProvider $aclProvider, Helper $helper, $identifier)
    {
        $show = $this->getEntity('shows', $identifier);
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

        return $this->redirectToRoute('edit_entity_admin',
            ['type' => 'shows', 'identifier' => $show->getSlug()]);
    }

    /**
     * Revoke an admin's access to a show.
     *
     * @Route("/{type<events|shows|societies|venues>}/{identifier}/admins/{uid}", methods={"DELETE"}, name="delete_entity_admin")
     */
    public function deleteAdminAction(Request $request, AclProvider $aclProvider, Helper $helper, $type, $identifier, $uid)
    {
        $entity = $this->getEntity($type, $identifier);
        $helper->ensureGranted('EDIT', $entity);

        if (!$this->isCsrfTokenValid('delete_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneById($uid);
        if ($user != null) {
            $aclProvider->revokeAccess($entity, $user, $this->getUser());
        } else {
            $this->addFlash('error', "Cannot delete admin: user ID not found. Try again or contact support.");
        }

        return $this->redirectToRoute('edit_entity_admin', compact('type', 'identifier'));
    }

    /**
     * Revoke a pending admin's access to an entity.
     *
     * @Route("/{type<events|shows|societies|venues>}/{identifier}/pending-admins/{uid}", methods={"DELETE"}, name="delete_pending_admin")
     */
    public function deletePendingAdminAction(Request $request, Helper $helper, $type, $identifier, $uid)
    {
        $entity = $this->getEntity($type, $identifier);
        $helper->ensureGranted('EDIT', $entity);

        if (!$this->isCsrfTokenValid('delete_pending_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $pending_admin = $em->getRepository(PendingAccess::class)->findOneById($uid);
        if ($pending_admin == null) {
            $this->addFlash('error', "Cannot delete pending admin: ID not found. Try again or contact support.");
        } else if ($pending_admin->getRid() != $entity->getId() ||
                $pending_admin->getType() != $entity->getEntityType()) {
            $this->addFlash('error', "Cannot delete pending admin: wrong entity. Try again or contact support.");
        } else {
            $em->remove($pending_admin);
            $em->flush();
        }

        return $this->redirectToRoute('edit_entity_admin', compact('type', 'identifier'));
    }
}
