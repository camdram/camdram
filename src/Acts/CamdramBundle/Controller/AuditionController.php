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
}
