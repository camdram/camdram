<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity\Application;
use Acts\CamdramBundle\Entity\Organisation;
use Acts\CamdramBundle\Form\Type\OrganisationApplicationType;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramSecurityBundle\Entity\PendingAccess;
use Acts\CamdramSecurityBundle\Event\CamdramSecurityEvents;
use Acts\CamdramSecurityBundle\Event\PendingAccessEvent;
use Acts\CamdramSecurityBundle\Form\Type\PendingAccessType;
use Acts\DiaryBundle\Diary\Diary;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Class OrganisationController
 *
 * Abstract controller for REST actions for organisations. Inherits from AbstractRestController.
 */
abstract class OrganisationController extends AbstractRestController
{

    /**
     * Render the Admin Panel
     * @Rest\NoRoute
     */
    public function adminPanelAction(Organisation $org)
    {
        $em = $this->getDoctrine()->getManager();
        $admins = $this->get('camdram.security.acl.provider')->getOwners($org);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($org);

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
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');

        return $this->view($news_repo->getRecentByOrganisation($org, 30), 200)
            ->setTemplateVar('news')
            ->setTemplate('organisation/news.html.twig')
            ;
    }

    abstract protected function getPerformances($slug, \DateTime $from, \DateTime $to);

    abstract protected function getShows($slug, \DateTime $from, \DateTime $to);

    /**
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     *
     * @return mixed
     */
    public function getShowsAction(Request $request, $identifier)
    {
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
     * @param $identifier
     *
     * @return mixed
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

        $view = $this->view($diary, 200)
            ->setTemplateVar('diary')
            ->setTemplate('organisation/diary.html.twig')
        ;

        return $view;
    }

    /**
     * Redirect from /events -> /diary for backwards compatibility
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
            $obj->setSociety($org);
        }
        $form = $this->createForm(OrganisationApplicationType::class, $obj, ['method' => $method]);

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
            ->setTemplate($this->type.'/application-new.html.twig');
    }

    /**
     * @param $identifier
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

            return $this->routeRedirectView('get_'.$this->type, array('identifier' => $org->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setData(array('org' => $org, 'form' => $form->createView()))
                ->setTemplate($this->type.'/application-new.html.twig');
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
        $form = $this->getApplicationForm($org, $application, 'PUT');

        return $this->view($form, 200)
            ->setData(array('org' => $org, 'form' => $form->createView()))
            ->setTemplate($this->type.'/application-edit.html.twig');
    }

    /**
     * @param $identifier
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

            return $this->routeRedirectView('get_'.$this->type, array('identifier' => $org->getSlug()));
        } else {
            return $this->view($form, 400)
                ->setTemplateVar('form')
                ->setTemplate($this->type.'/application-edit.html.twig');
        }
    }

    public function deleteApplicationAction(Request $request, $identifier)
    {
        $this->checkAuthenticated();
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('DELETE', $org);

        if (!$this->isCsrfTokenValid('delete_application', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

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
        $admins = $em->getRepository('ActsCamdramSecurityBundle:User')->getEntityOwners($org);
        $pending_admins = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findByResource($org);

        return $this->view($form, 200)
            ->setData(
                array(
                'entity' => $org,
                'admins' => $admins,
                'pending_admins' => $pending_admins,
                'form' => $form->createView())
            )
            ->setTemplate('pending_access/edit.html.twig');
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
                $existing_user = $em->getRepository('ActsCamdramSecurityBundle:User')
                                    ->findOneByEmail($pending_ace->getEmail());

                if ($existing_user != null) {
                    $this->get('camdram.security.acl.provider')
                        ->grantAccess($org, $existing_user, $this->getUser());
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
                        $event_dispatcher->dispatch(
                            CamdramSecurityEvents::PENDING_ACCESS_CREATED,
                            new PendingAccessEvent($pending_ace)
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

        return $this->routeRedirectView($route, array('identifier' => $org->getSlug()));
    }

    /**
     * Revoke an admin's access to an organisation.
     *
     * @Rest\Delete
     */
    public function deleteAdminAction(Request $request, $identifier, $uid)
    {
        $org = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $org);

        if (!$this->isCsrfTokenValid('delete_admin', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('ActsCamdramSecurityBundle:User')->findOneById($uid);
        if ($user != null) {
            $this->get('camdram.security.acl.provider')->revokeAccess($org, $user, $this->getUser());
        }
        if ($org->getEntityType() == 'society') {
            $route = 'edit_society_admin';
        } else {
            $route = 'edit_venue_admin';
        }

        return $this->routeRedirectView($route, array('identifier' => $org->getSlug()));
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
        $pending_admin = $em->getRepository('ActsCamdramSecurityBundle:PendingAccess')->findOneById($uid);
        if ($pending_admin != null) {
            $em->remove($pending_admin);
            $em->flush();
        }
        if ($org->getEntityType() == 'society') {
            $route = 'edit_society_admin';
        } else {
            $route = 'edit_venue_admin';
        }

        return $this->routeRedirectView($route, array('identifier' => $org->getSlug()));
    }

    /**
     * View a list of the organisation's last shows.
     * @Rest\Get(requirements={"_format"="html"})
     */
    public function getHistoryAction(Request $request, $identifier) {
        $showsPerPage = 36;

        $org = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $org);
        // Casting string→int in PHP always succeeds so no try/catch needed.
        $page = $request->query->has("p") ? max(1, (int) $request->query->get("p")) : 1;

        $qb = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show')
              ->queryByOrganisation($org, new \DateTime('1970-01-01'), new \DateTime('yesterday'))
              ->orderBy('p.start_at', 'DESC')->addOrderBy('s.id') // Make deterministic
              ->setFirstResult($showsPerPage * ($page - 1))
              ->setMaxResults($showsPerPage);
        $paginator = new Paginator($qb->getQuery());
        $route = explode('?', $request->getRequestUri())[0] . '?p=';

        return $this->view([
            'org' => $org,
            'paginator' => $paginator,
            'page_num' => $page,
            'page_urlprefix' => $route
        ], 200)->setTemplate('organisation/past-shows.html.twig');
    }
}
