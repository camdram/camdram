<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;
use Acts\CamdramBundle\Service\ModerationManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SocietyController
 *
 * Controller for REST actions for societies. Inherits from AbstractRestController.
 * @Route("/societies")
 */
class SocietyController extends OrganisationController
{
    protected $class = Society::class;

    protected $type = 'society';

    protected $type_plural = 'societies';

    protected $search_index = 'society';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository(Society::class);
    }

    protected function getForm($society = null, $method = 'POST')
    {
        return $this->createForm(SocietyType::class, $society, ['method' => $method]);
    }

    /**
     * @Route("/{identifier}.{_format}", format="html", methods={"GET"}, name="get_society",
     *      condition="request.getPathInfo() != '/societies/new'")
     */
    public function getAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $can_contact = !empty($this->getDoctrine()->getRepository(\Acts\CamdramSecurityBundle\Entity\User::class)
            ->getContactableEntityOwners($society));

        return $this->doGetAction($society, ['can_contact' => $can_contact]);
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     *
     * @Route("/by-id/{id}.{_format}", format="html", methods={"GET"}, name="get_society_by_id")
     */
    public function getByIdAction(Request $request, $id)
    {
        $this->checkAuthenticated();
        $society = $this->getRepository()->findOneById($id);

        if (!$society)
        {
            throw $this->createNotFoundException('That society id does not exist');
        }

        return $this->redirectToRoute('get_society', ['identifier' => $society->getSlug(), '_format' => $request->getRequestFormat()]);
    }

    /**
     * @Route(".{_format}", format="html", methods={"GET"}, name="get_societies")
     */
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return $this->entitySearch($request);
        }

        $societies = $this->getRepository()->findAllOrderedByCollegeName();
        return $this->show('society/index.html.twig', 'societies', $societies);
    }

    /**
     * @Route("/{identifier}/vacancies.{_format}", methods={"GET"}, name="get_society_vacancies")
     */
    public function getVacanciesAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository(Entity\Audition::class);
        $techie_repo = $this->getDoctrine()->getRepository(Entity\TechieAdvert::class);
        $applications_repo = $this->getDoctrine()->getRepository(Entity\Application::class);
        $now = new \DateTime;

        $data = [
            'society' => $society,
            'auditions' => $auditions_repo->findUpcomingBySociety($society, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledBySociety($society, 10, $now),
            'techie_ads' => $techie_repo->findLatestBySociety($society, 10, $now),
            'app_ads' => $applications_repo->findLatestBySociety($society, 10, $now),
        ];

        return $this->show('society/vacancies.html.twig', 'data', $data);
    }

    /**
     * Finds all performances by the selected society (used by OrganisationController).
     */
    protected function getPerformances($slug, \DateTime $from, \DateTime $to)
    {
        $performance_repo = $this->getDoctrine()->getRepository(Entity\Performance::class);

        return $performance_repo->getBySociety($this->getEntity($slug), $from, $to);
    }

    /**
     * Finds all shows by the selected society (used by OrganisationController).
     */
    protected function getShows($slug, \DateTime $from, \DateTime $to)
    {
        $show_repo = $this->getDoctrine()->getRepository(Entity\Show::class);

        return $show_repo->getBySociety($this->getEntity($slug), $from, $to);
    }

    /**
     * @Route("/{identifier}/image", methods={"DELETE"}, name="delete_society_image")
     */
    public function deleteImageAction(Request $request, $identifier)
    {
        $society = $this->getEntity($identifier);
        $this->get('camdram.security.acl.helper')->ensureGranted('EDIT', $society);

        if (!$this->isCsrfTokenValid('delete_society_image', $request->request->get('_token'))) {
            throw new BadRequestHttpException('Invalid CSRF token');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($society->getImage());
        $society->setImage(null);
        $em->flush();

        return $this->redirectToRoute('get_society', ['identifier' => $identifier]);
    }

    /**
     * Revoke a pending admin's access to an organisation.
     *
     * @Route("/{identifier}/pending-admins/{uid}", methods={"DELETE"}, name="delete_society_pending_admin")
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        return parent::deletePendingAdminAction($request, $identifier, $uid);
    }

    /**
      * @Route("/{identifier}/admins", methods={"POST"}, name="post_society_admin")
      */
    public function postAdminAction(Request $request, $identifier,
        ModerationManager $moderation_manager, EventDispatcherInterface $event_dispatcher)
    {
        return parent::postAdmin($request, $identifier, $moderation_manager, $event_dispatcher);
    }
}
