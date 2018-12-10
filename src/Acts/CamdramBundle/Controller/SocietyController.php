<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use FOS\RestBundle\Controller\Annotations as Rest;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;

/**
 * Class SocietyController
 *
 * Controller for REST actions for societies. Inherits from AbstractRestController.
 *
 * @Rest\RouteResource("Society")
 */
class SocietyController extends OrganisationController
{
    protected $class = Society::class;

    protected $type = 'society';

    protected $type_plural = 'societies';

    protected $search_index = 'society';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Society');
    }

    protected function getForm($society = null, $method = 'POST')
    {
        return $this->createForm(SocietyType::class, $society, ['method' => $method]);
    }

    public function getAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $this->denyAccessUnlessGranted('VIEW', $society);
        
        $can_contact = $this->getDoctrine()->getRepository('ActsCamdramSecurityBundle:User')
            ->getContactableEntityOwners($society) > 0;
        
        $view = $this->view($society, 200)
            ->setTemplate('society/show.html.twig')
            ->setTemplateData(['society' => $society, 'can_contact' => $can_contact])
        ;
        
        return $view;
    }

    /**
     * Action that allows querying by id. Redirects to slug URL
     * 
     * @Rest\Get("/societies/by-id/{id}")
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
    
    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return parent::cgetAction($request);
        }

        $societies = $this->getRepository()->findAllOrderedByCollegeName();

        $view = $this->view($societies, 200)
            ->setTemplateVar('societies')
            ->setTemplate('society/index.html.twig')
        ;

        return $view;
    }

    public function getVacanciesAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition');
        $techie_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
        $applications_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application');
        $now = new \DateTime;

        $data = array(
            'society' => $society,
            'auditions' => $auditions_repo->findUpcomingBySociety($society, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledBySociety($society, 10, $now),
            'techie_ads' => $techie_repo->findLatestBySociety($society, 10, $now),
            'app_ads' => $applications_repo->findLatestBySociety($society, 10, $now),
        );

        return $this->view($data, 200)
            ->setTemplateVar('vacancies')
            ->setTemplate('society/vacancies.html.twig')
            ;
    }

    /**
     * Finds all performances by the selected society (used by OrgansiationController).
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

        return $performance_repo->getBySociety($this->getEntity($slug), $from, $to);
    }

    /**
     * Finds all shows by the selected society (used by OrgansiationController).
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

        return $show_repo->getBySociety($this->getEntity($slug), $from, $to);
    }

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
     * @Rest\Delete("/societies/{identifier}/pending-admins/{uid}")
     */
    public function deletePendingAdminAction(Request $request, $identifier, $uid)
    {
        return parent::deletePendingAdminAction($request, $identifier, $uid);
    }
}
