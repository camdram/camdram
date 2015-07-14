<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Application;

/**
 * @RouteResource("Application")
 */
class ApplicationController extends FOSRestController
{
    /**
     * cgetAction
     *
     * Display application deadlines from now until the end of (camdram) time
     */
    public function cgetAction()
    {
        $applications = array_reverse($this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findLatest(-1, new \DateTime()));

        $view = $this->view($applications, 200)
            ->setTemplate('ActsCamdramBundle:Application:index.html.twig')
            ->setTemplateVar('applications')
        ;

        return $view;
    }

    public function getAction($identifier)
    {
        $application = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findOneBySlug($identifier, new \DateTime());
        if ($application) {
            return $this->redirect($this->generateUrl('get_applications').'#'.$application->getSlug());
        } else {
            throw $this->createNotFoundException('No appplication advert exists with that identifier');
        }
    }

    /**
     * weeksApplicationsAction
     *
     * Generates the table data for displaying application deadlines in
     * the week beggining with $startOfWeek
     *
     * @param DateTime $startOfWeek The start date of the week.
     */
    public function weeksApplicationsAction($startOfWeek)
    {
        $startDate = $startOfWeek->getTimestamp();
        $endDate = clone $startOfWeek;
        $endDate = $endDate->modify('+6 days')->getTimestamp();

        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Application');

        $applications = $repo->findScheduledOrderedByDeadline($startDate, $endDate);

        $view = $this->view(array('startDate' => $startDate, 'endDate' => $endDate, 'applications' => $applications), 200)
            ->setTemplate('ActsCamdramBundle:Application:diary.html.twig')
            ->setTemplateVar('applications')
        ;

        return $view;
    }
}
