<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramBundle\Form\Type\OrganisationApplicationType;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Entity\User;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use Acts\DiaryBundle\Diary\Diary;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class OrganisationController
 *
 * Abstract controller for REST actions for organisations. Inherits from AbstractRestController.
 * @template T of \Acts\CamdramBundle\Entity\Organisation
 * @extends AbstractRestController<T>
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
        $pending_admins = $em->getRepository(PendingAccess::class)->findByResource($org);

        return $this->render(
            $this->type.'/admin-panel.html.twig',
            array('org' => $org,
                'admins' => $admins,
                'pending_admins' => $pending_admins)
        );
    }

    public function getNewsAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $news_repo = $this->getDoctrine()->getRepository(Entity\News::class);

        return $this->show('organisation/news.html.twig', 'news', $news_repo->getRecentByOrganisation($org, 30));
    }

    abstract protected function getPerformances($slug, \DateTime $from, \DateTime $to);

    abstract protected function getShows($slug, \DateTime $from, \DateTime $to);

    /**
     * Render a diary of the shows put on by this society.
     * @Route("/{identifier}/shows.{_format}", format="html", methods={"GET"})
     */
    public function getShowsAction(Request $request, $identifier)
    {
        if ($request->getRequestFormat() == 'html') {
            throw new NotFoundHttpException("This is part of our API, add a .json or .xml suffix.");
        }
        try {
            if ($request->query->has('from')) {
                $from = new \DateTime($request->query->get('from'));
            } else {
                $from = new \DateTime;
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad from parameter, try YYYY-MM-DD format.");
        }

        try {
            if ($request->query->has('to')) {
                $to = new \DateTime($request->query->get('to'));
            } else {
                $to = clone $from;
                $to->modify('+1 year');
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad to parameter, try YYYY-MM-DD format.");
        }

        $shows = $this->getShows($identifier, $from, $to);

        return $this->view($shows, 200);
    }

    /**
     * Render a diary of the shows put on by this society.
     *
     * @Route("/{identifier}/diary.{_format}", format="html")
     */
    public function getDiaryAction(Request $request, $identifier)
    {
        $diary = new Diary;

        try {
            if ($request->query->has('from')) {
                $from = new \DateTime($request->query->get('from'));
            } else {
                $from = new \DateTime;
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad from parameter, try YYYY-MM-DD format.");
        }

        try {
            if ($request->query->has('to')) {
                $to = new \DateTime($request->query->get('to'));
            } else {
                $to = clone $from;
                $to->modify('+1 year');
            }
        } catch (\Exception $e) {
            throw new BadRequestHttpException("Bad to parameter, try YYYY-MM-DD format.");
        }

        $performances = $this->getPerformances($identifier, $from, $to);
        $diary->addEvents($performances);

        return $this->show('organisation/diary.html.twig', 'diary', $diary);
    }

    /**
     * Redirect from /events -> /diary for backwards compatibility
     * @Route("/{identifier}/events.{_format}", format="html")
     */
    public function getEventsAction(Request $request, $identifier)
    {
        return $this->redirect($this->generateUrl('get_'.$this->type.'_diary',
            ['identifier' => $identifier, '_format' => $request->getRequestFormat()]));
    }

    private function getApplicationForm(Organisation $org, $obj = null, $method = 'POST')
    {
        if (!$obj) {
            $obj = new Application();
            if ($org instanceof Society) {
                $obj->setSociety($org);
            } else {
                $obj->setVenue($org);
            }
        }
        $form = $this->createForm(OrganisationApplicationType::class, $obj, ['method' => $method]);

        return $form;
    }

    /**
     * @Route("/{identifier}/applications/new", methods={"GET"})
     */
    public function newApplicationAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getApplicationForm($org);

        return $this->render($this->type.'/application-new.html.twig',
            ['org' => $org, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{identifier}/applications", methods={"POST"})
     */
    public function postApplicationAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $form = $this->getApplicationForm($org);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('get_'.$this->type, array('identifier' => $org->getSlug()));
        } else {
            return $this->render($this->type.'/application-new.html.twig',
                ['org' => $org, 'form' => $form->createView()]);
        }
    }

    /**
     * @Route("/{identifier}/application/edit", methods={"GET"})
     */
    public function editApplicationAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $application = $org->getApplications()->first();
        $form = $this->getApplicationForm($org, $application, 'PUT');

        return $this->render($this->type.'/application-edit.html.twig',
            ['org' => $org, 'form' => $form->createView()]);
    }

    /**
     * @Route("/{identifier}/application", methods={"PUT"})
     */
    public function putApplicationAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $application = $org->getApplications()->first();
        $form = $this->getApplicationForm($org, $application, 'PUT');
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute('get_'.$this->type, array('identifier' => $org->getSlug()));
        } else {
            return $this->render($this->type.'/application-edit.html.twig',
               ['form' => $form->createView()])->setStatusCode(400);
        }
    }

    /**
     * @Route("/{identifier}/application", methods={"DELETE"})
     */
    public function deleteApplicationAction(Request $request, $identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $org);

        if (!$this->isCsrfTokenValid('delete_application', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $application = $org->getApplications()->first();
        $em = $this->getDoctrine()->getManager();
        $em->remove($application);
        $em->flush();

        return $this->redirectToRoute('get_'.$this->type, array('identifier' => $org->getSlug()));
    }

    /**
     * Get a form for adding an admin to an organisation.
     *
     * @Route("/{identifier}/admin/edit")
     */
    public function editAdminAction($identifier)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);
        if ($org->getEntityType() == 'society') {
            $route = 'post_society_admin';
        } else {
            $route = 'post_venue_admin';
        }

        $ace = new PendingAccess();
        $ace->setRid($org->getId());
        $ace->setType($org->getEntityType());
        $ace->setIssuer($this->getUser());
        $form = $this->createForm(PendingAccessType::class, $ace, array(
            'action' => $this->generateUrl($route, array('identifier' => $identifier))));

        $em = $this->getDoctrine()->getManager();
        $admins = $em->getRepository(User::class)->getEntityOwners($org);
        $pending_admins = $em->getRepository(PendingAccess::class)->findByResource($org);

        return $this->render('pending_access/edit.html.twig', [
            'entity' => $org,
            'admins' => $admins,
            'pending_admins' => $pending_admins,
            'form' => $form->createView()]);
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
     */
    public function postAdmin(Request $request, $identifier,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        $pending_ace = new PendingAccess();
        $form = $this->createForm(PendingAccessType::class, $pending_ace);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /* Check if the ACE doesn't need to be created for various reasons. */
            /* Is this person already an admin? */
            $already_admin = false;
            $admins = $moderation_manager->getModeratorsForEntity($org);
            foreach ($admins as $admin) {
                if ($admin->getEmail() == $pending_ace->getEmail()) {
                    $already_admin = true;
                    break;
                }
            }
            if ($already_admin == false) {
                /* If this person is already a Camdram user then grant access immediately. */
                $em = $this->getDoctrine()->getManager();
                $existing_user = $em->getRepository(User::class)
                                    ->findOneByEmail($pending_ace->getEmail());

                if ($existing_user != null) {
                    $this->get('camdram.security.acl.provider')
                        ->grantAccess($org, $existing_user, $this->getUser());
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
            }
        }
        if ($org->getEntityType() == 'society') {
            $route = 'get_society';
        } else {
            $route = 'get_venue';
        }

        return $this->redirectToRoute($route, array('identifier' => $org->getSlug()));
    }

    /**
     * Revoke an admin's access to an organisation.
     * @Route("/{identifier}/admins/{uid}", methods={"DELETE"})
     */
    public function deleteAdminAction(Request $request, $identifier, $uid)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        if (!$this->isCsrfTokenValid('delete_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneById($uid);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->revokeAccess($org, $user, $this->getUser());
        }
        if ($org->getEntityType() == 'society') {
            $route = 'acts_camdram_society_editadmin';
        } else {
            $route = 'acts_camdram_venue_editadmin';
        }

        return $this->redirectToRoute($route, array('identifier' => $org->getSlug()));
    }

    /**
     * Revoke a pending admin's access to an organisation.
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        if (!$this->isCsrfTokenValid('delete_pending_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $pending_admin = $em->getRepository(PendingAccess::class)->findOneById($uid);
        if ($pending_admin != null) {
            $em->remove($pending_admin);
            $em->flush();
        }
        if ($org->getEntityType() == 'society') {
            $route = 'acts_camdram_society_editadmin';
        } else {
            $route = 'acts_camdram_venue_editadmin';
        }

        return $this->redirectToRoute($route, array('identifier' => $org->getSlug()));
    }

    /**
     * View a list of the organisation's last shows.
     * @Route("/{identifier}/history.{_format}", format="html")
     */
    public function getHistoryAction(Request $request, $identifier) {
        $showsPerPage = 36;

        $org = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $org);
        // Casting string→int in PHP always succeeds so no try/catch needed.
        $page = $request->query->has("p") ? max(1, (int) $request->query->get("p")) : 1;

        $qb = $this->getDoctrine()->getRepository(Entity\Show::class)
              ->queryByOrganisation($org, new \DateTime('1970-01-01'), new \DateTime('yesterday'))
              ->select('s, perf')->leftJoin('s.performances', 'perf')
              ->orderBy('p.start_at', 'DESC')->addOrderBy('s.id') // Make deterministic
              ->setFirstResult($showsPerPage * ($page - 1))
              ->setMaxResults($showsPerPage);
        $paginator = new Paginator($qb->getQuery());
        $route = explode('?', $request->getRequestUri())[0] . '?p=';

        return $this->show('organisation/past-shows.html.twig', 'data', [
            'org' => $org,
            'paginator' => $paginator,
            'page_num' => $page,
            'page_urlprefix' => $route
        ]);
    }
}
