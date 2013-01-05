<?php
 
namespace Acts\CamdramBundle\Controller;
 
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Audition;

use Doctrine\Common\Collections\Criteria;
 

/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction()
    {
 
        //$auditions = $repo->findScheduledJoinedToShow($startDate, $endDate);
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findById(72);

        $view = $this->view(array('startDate' => null, 'auditions' => $auditions), 200)
                  ->setTemplate("ActsCamdramBundle:Audition:index.html.twig")
                  ->setTemplateVar('auditions')
        ;
        return $view;
    }
    
    /**
     * weeksPerformancesAction
     *
     * Generates the table data for displaying the show performances in 
     * the week beggining with $startOfWeek
     *
     * @param DateTime $startOfWeek The start date of the week.
     */
    public function weeksAuditionsAction($startOfWeek)
    {
        $startDate = $startOfWeek->getTimestamp();
        $endDate = clone $startOfWeek;
        $endDate = $endDate->modify("+6 days")->getTimestamp();

        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Audition');
        
        $auditions = $repo->findScheduledJoinedToShow($startDate, $endDate);

        $view = $this->view(array('startDate' => $startDate, 'endDate' => $endDate, 'auditions' => $auditions), 200)
            ->setTemplate("ActsCamdramBundle:Audition:index.html.twig")
            ->setTemplateVar('auditions')
        ;
        
        return $view;
    }
}
