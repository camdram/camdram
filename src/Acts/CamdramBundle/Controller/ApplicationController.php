<?php
 
namespace Acts\CamdramBundle\Controller;
 
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Application;

use Doctrine\Common\Collections\Criteria;
 

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
        $startDate = 
        $applications = $this->getDoctrine()->getRepository('ActsCamdramBundle:Application')
            ->findScheduledOrderedByDeadline(new \DateTime(), new \DateTime("2034/1/1"));

        $view = $this->view($applications, 200)
            ->setTemplate("ActsCamdramBundle:Application:index.html.twig")
            ->setTemplateVar('applications')
        ;
        return $view;
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
        $endDate = $endDate->modify("+6 days")->getTimestamp();

        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Application');
        
        $applications = $repo->findScheduledOrderedByDeadline($startDate, $endDate);

        $view = $this->view(array('startDate' => $startDate, 'endDate' => $endDate, 'applications' => $applications), 200)
            ->setTemplate("ActsCamdramBundle:Application:diary.html.twig")
            ->setTemplateVar('applications')
        ;
        
        return $view;
    }
}
