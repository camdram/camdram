<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;

/**
 * Class SocietyController
 *
 * Controller for REST actions for societies. Inherits from AbstractRestController.
 *
 * @RouteResource("Society")
 */
class SocietyController extends OrganisationController
{
    protected $class = 'Acts\\CamdramBundle\\Entity\\Society';

    protected $type = 'society';

    protected $type_plural = 'societies';

    protected $search_index = 'society';

    protected function getRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Society');
    }

    protected function getForm($society = null)
    {
        return $this->createForm(new SocietyType(), $society);
    }

    public function cgetAction(Request $request)
    {
        if ($request->query->has('q')) {
            return parent::cgetAction($request);
        }

        $societies = $this->getRepository()->findAllOrderedByCollegeName();

        $view = $this->view($societies, 200)
            ->setTemplateVar('societies')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':index.html.twig')
        ;

        return $view;
    }

    public function getVacanciesAction($identifier)
    {
        $society = $this->getEntity($identifier);
        $auditions_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition');
        $techie_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert');
        $applications_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application');
        $now = $this->get('acts.time_service')->getCurrentTime();

        $data = array(
            'society' => $society,
            'auditions' => $auditions_repo->findUpcomingBySociety($society, 10, $now),
            'nonscheduled_auditions' => $auditions_repo->findUpcomingNonScheduledBySociety($society, 10, $now),
            'techie_ads' => $techie_repo->findLatestBySociety($society, 10, $now),
            'app_ads' => $applications_repo->findLatestBySociety($society, 10, $now),
        );

        return $this->view($data, 200)
            ->setTemplateVar('vacancies')
            ->setTemplate('ActsCamdramBundle:Society:vacancies.html.twig')
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
}
