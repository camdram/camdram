<?php
 
namespace Acts\CamdramBundle\Controller;
 
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\TechieAdvert;

use Doctrine\Common\Collections\Criteria;
 

/**
 * @RouteResource("Techie")
 */
class TechieAdvertController extends FOSRestController
{
    /**
     * cgetAction
     *
     * Display technician adverts from now until the end of (camdram) time
     */
    public function cgetAction()
    {
        $startDate = 
        $techieAdverts = $this->getDoctrine()->getRepository('ActsCamdramBundle:TechieAdvert')
            ->findCurrentOrderedByDateName();

        $view = $this->view($techieAdverts, 200)
            ->setTemplate("ActsCamdramBundle:TechieAdvert:index.html.twig")
            ->setTemplateVar('techieadverts')
        ;
        return $view;
    }
}
