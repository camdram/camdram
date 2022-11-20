<?php

namespace Acts\CamdramBundle\Controller;

use Acts\CamdramBundle\Entity;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;
use Acts\CamdramBundle\Service\ModerationManager;
use Acts\CamdramBundle\Service\Time;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SocietyController
 *
 * Controller for REST actions for societies. Inherits from AbstractRestController.
 * @Route("/societies")
 * @extends OrganisationController<Society>
 */
class SocietyController extends OrganisationController
{
    protected $class = Society::class;

    protected $type = 'society';

    protected $type_plural = 'societies';

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
        $can_contact = $society->getContactEmail() != null ||
            !empty($this->getDoctrine()->getRepository(\Acts\CamdramSecurityBundle\Entity\User::class)
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

        $time = Time::now()->modify('-18 months');
        $societies = $this->em->createQuery(
            'SELECT s FROM Acts\\CamdramBundle\\Entity\\Society s ORDER BY s.college, s.name'
        )
            ->getResult();
        if ($request->getRequestFormat() != 'html') {
            return $this->view($societies);
        }

        $cmpIds = function($a, $b) {
            return (is_array($a) ? $a['id'] : $a->getId()) <=> (is_array($b) ? $b['id'] : $b->getId());
        };

        $societiesWithShows = $this->em->createQuery(
            'SELECT s.id as id FROM Acts\\CamdramBundle\\Entity\\Society s JOIN s.shows show ' .
                'WHERE show IN (SELECT x FROM Acts\\CamdramBundle\\Entity\\Performance p JOIN p.show x WHERE p.repeat_until > :time)'
        )->setParameters(['time' => $time])->getResult();

        $societiesWithAdmins = $this->em->createQuery(
            'SELECT a.entityId AS id FROM Acts\\CamdramSecurityBundle\\Entity\\AccessControlEntry a ' .
            'WHERE a.type = \'society\' AND a.user IN '.
            '(SELECT u FROM Acts\\CamdramSecurityBundle\\Entity\\User u WHERE u.last_login_at > :time)'
        )->setParameters(['time' => $time])->getResult();

        $oldSocieties = array_udiff($societies, $societiesWithShows, $societiesWithAdmins, $cmpIds);
        $currentSocieties = array_udiff($societies, $oldSocieties, $cmpIds);

        return $this->render('society/index.html.twig',
            compact('currentSocieties', 'oldSocieties'));
    }

    /**
     * @Route("/{identifier}/vacancies.{_format}", methods={"GET"}, name="get_society_vacancies")
     */
    public function getVacanciesAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $repo = $this->getDoctrine()->getRepository(Entity\Advert::class);
        $now = new \DateTime;

        $data = [
            'society' => $society,
            'adverts' => $repo->findLatestBySociety($society, 10, $now),
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
}
