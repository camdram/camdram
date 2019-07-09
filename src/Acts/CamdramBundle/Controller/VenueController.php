<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Acts\CamdramBundle\Entity\Venue;
use Acts\CamdramBundle\Form\Type\VenueType;
use Acts\CamdramBundle\Service\ModerationManager;

/**
 * Class VenueController
 *
 * Controller for REST actions for venues. Inherits from AbstractRestController.
 *
 * @Rest\RouteResource("Venue")
 */
class VenueController extends OrganisationController
{
    protected $class = Venue::class;

    protected $type = 'venue';

    protected $type_plural = 'venues';

    protected $search_index = 'venue';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
    }

    protected function getForm($venue = null, $method = 'POST')
    {
        return $this->createForm(VenueType::class, $venue, ['method' => $method]);
    }

    public function getAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $venue);

        $can_contact = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User')
            ->getContactableEntityOwners($venue) > 0;

        $view = $this->view($venue, 200)
            ->setTemplate('venue/show.html.twig')
            ->setTemplateData(['venue' => $venue, 'can_contact' => $can_contact])
        ;

        return $view;
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     *
     * @Rest\Get("/venues/by-id/{id}")
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->checkAuthenticated();
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
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return $this->entitySearch($request);
        }

        $venues = $this->getRepository()->findAllOrderedByName();

        $view = $this->view($venues, 200)
            ->setTemplateVar('venues')
            ->setTemplate('venue/index.html.twig')
        ;

        return $view;
    }

    public function getVacanciesAction($identifier)
    {
        $venue = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition');
        $techie_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
        $applications_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application');
        $now = new \DateTime();

        $data = array(
            'venue' => $venue,
            'auditions' => $auditions_repo->findUpcomingByVenue($venue, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledByVenue($venue, 10, $now),
            'techie_ads' => $techie_repo->findLatestByVenue($venue, 10, $now),
            'app_ads' => $applications_repo->findLatestByVenue($venue, 10, $now),
        );

        return $this->view($data, 200)
            ->setTemplateVar('vacancies')
            ->setTemplate('venue/vacancies.html.twig')
        ;
    }

    /**
     * Finds all performances in the selected venue (used by OrgansiationController).
     *
     * @param $slug
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    protected function getPerformances($slug, \DateTime $from, \DateTime $to)
    {
        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');

        return $performance_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

    /**
     * Finds all shows in the selected venue (used by OrgansiationController).
     *
     * @param $slug
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return mixed
     */
    protected function getShows($slug, \DateTime $from, \DateTime $to)
    {
        $show_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Show');

        return $show_repo->getByVenue($this->getEntity($slug), $from, $to);
    }

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
     * @Rest\Delete("/venues/{identifier}/pending-admins/{uid}")
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        return parent::deletePendingAdminAction($request, $identifier, $uid);
    }

    /**
      * @Rest\Post("/venues/{identifier}/admins", name="post_venue_admin")
      * @param $identifier
      */
    public function postAdminAction(Request $request, $identifier,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher)
    {
        return parent::postAdmin($request, $identifier, $moderation_manager, $event_dispatcher);
    }
}
