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
class SocietyController extends AbstractRestController
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

    /**
     * Render a diary of the shows put on by this society.
     *
     * @param $identifier
     * @return mixed
     */
    public function getShowsAction($identifier)
    {
        $performance_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:Performance');
        $now = $this->get('acts.time_service')->getCurrentTime();
        $performances = $performance_repo->getUpcomingBySociety($now, $this->getEntity($identifier));

        $diary = $this->get('acts.diary.factory')->createDiary();

        $events = $this->get('acts.camdram.diary_helper')->createEventsFromPerformances($performances);
        $diary->addEvents($events);

        $view = $this->view($diary, 200)
            ->setTemplateVar('diary')
            ->setTemplate('ActsCamdramBundle:'.$this->getController().':shows.html.twig')
        ;

        return $view;
    }
}
