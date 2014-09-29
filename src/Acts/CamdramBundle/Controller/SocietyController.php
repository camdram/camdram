<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;
use Acts\CamdramBundle\Form\Type\SocietyType;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     * @return mixed
     */
    public function getShowsAction(Request $request, $identifier)
    {
        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
               
        if($request->query->get('from')){
            $from = new \DateTime($request->query->get('from'));
        }else{
            $from = $this->get('acts.time_service')->getCurrentTime();
        }
        
        if($request->query->get('to')){
            $to = new \DateTime($request->query->get('to'));
        }else{
            $to = null;
        }
        
        $performances = $performance_repo->getBySociety($this->getEntity($identifier),$from,$to);

        $diary = $this->get('acts.diary.factory')->createDiary();

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromPerformances($performances);
        $diary->addEvents($events);

        $view = $this->view($diary, 200)
            ->setTemplateVar('diary')
            ->setTemplate('ActsCamdramBundle:Organisation:shows.html.twig')
        ;

        return $view;
    }
}
