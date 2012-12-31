<?php
 
namespace Acts\CamdramBundle\Controller;
 
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Audition;
 
/**
 * @RouteResource("Audition")
 */
class AuditionController extends FOSRestController
{
    public function cgetAction()
    {
 
        //$auditions = $repo->findScheduledJoinedToShow($startDate, $endDate);
        $auditions = $this->getDoctrine()->getRepository('ActsCamdramBundle:Audition')->findAll();
        $view = $this->view(array('startDate' => null, 'auditions' => $auditions), 200)
                  ->setTemplate("ActsCamdramBundle:Audition:index.html.twig")
                  ->setTemplateVar('auditions')
        ;
        return $this->render('ActsCamdramBundle:Audition:index.html.twig', array('startDate' => null, 'auditions' => $auditions));
//die('xyz'); 
        return $view;
    }
}
