<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Form\Type\VenueType;
use Acts\CamdramBundle\Service\ModerationManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VenueController
 *
 * Controller for REST actions for venues. Inherits from AbstractRestController.
 * @Route("/venues")
 * @extends OrganisationController<Venue>
 */
class VenueController extends OrganisationController
{
    protected $class = Venue::class;

    protected $type = 'venue';

    protected $type_plural = 'venues';

    protected function getForm($venue = null, $method = 'POST')
    {
        return $this->createForm(VenueType::class, $venue, ['method' => $method]);
    }

    /**
     * @Route("/{identifier}.{_format}", format="html", methods={"GET"}, name="get_venue",
     *      condition="request.getPathInfo() != '/venues/new'"))
     */
    public function getAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $can_contact = !empty($this->getDoctrine()->getRepository(\Acts\CamdramSecurityBundle\Entity\User::class)
            ->getContactableEntityOwners($venue));

        return $this->doGetAction($venue, ['can_contact' => $can_contact]);
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     *
     * @Route("/by-id/{id}.{_format}", format="html", methods={"GET"}, name="get_venue_by_id")
     */
    public function getByIdAction(Request $request, $id)
    {
        $venue = $this->getRepository()->findOneById($id);

        if (!$venue)
        {
            throw $this->createNotFoundException('That venue id does not exist');
        }

        return $this->redirectToRoute('get_venue', ['identifier' => $venue->getSlug(), '_format' => $request->getRequestFormat()]);
    }

    /**
     * We don't want the default behaviour of paginated results - just output all of them unless there's a query
     * parameter specified.
     * @Route(".{_format}", format="html", methods={"GET"}, name="get_venues")
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return $this->entitySearch($request);
        }

        $venues = $this->getRepository()->findAllOrderedByName();
        return $this->show('venue/index.html.twig', 'venues', $venues);
    }

    public function getVacanciesAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository(Entity\Audition::class);
        $techie_repo = $this->getDoctrine()->getRepository(Entity\TechieAdvert::class);
        $applications_repo = $this->getDoctrine()->getRepository(Entity\Application::class);
        $now = new \DateTime();

        $data = array(
            'venue' => $venue,
            'auditions' => $auditions_repo->findUpcomingByVenue($venue, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledByVenue($venue, 10, $now),
            'techie_ads' => $techie_repo->findLatestByVenue($venue, 10, $now),
            'app_ads' => $applications_repo->findLatestByVenue($venue, 10, $now),
        );

        return $this->show('venue/vacancies.html.twig', 'data', $data);
    }

    /**
     * Finds all performances in the selected venue (used by OrganisationController).
     */
    protected function getPerformances($slug, \DateTime $from, \DateTime $to)
    {
        $performance_repo = $this->getDoctrine()->getRepository(Entity\Performance::class);

        return $performance_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

    /**
     * Finds all shows in the selected venue (used by OrganisationController).
     */
    protected function getShows($slug, \DateTime $from, \DateTime $to)
    {
        $show_repo = $this->getDoctrine()->getRepository(Entity\Show::class);

        return $show_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

    /**
     * @Route("/{identifier}/image", methods={"DELETE"}, name="delete_venue_image")
     */
    public function deleteImageAction(Request $request, $identifier)
    {
        $venue = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $venue);

        if (!$this->isCsrfTokenValid('delete_venue_image', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($venue->getImage());
        $venue->setImage(null);
        $em->flush();

        return $this->redirectToRoute('get_venue', ['identifier' => $identifier]);
    }

    /**
     * Revoke a pending admin's access to an organisation.
     *
     * @Route("/{identifier}/pending-admins/{uid}", methods={"DELETE"}, name="delete_venue_pending_admin")
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        return parent::deletePendingAdminAction($request, $identifier, $uid);
    }

    /**
     * @Route("/{identifier}/admins", methods={"POST"}, name="post_venue_admin")
     */
    public function postAdminAction(Request $request, $identifier,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher)
    {
        return parent::postAdmin($request, $identifier, $moderation_manager, $event_dispatcher);
    }
}
