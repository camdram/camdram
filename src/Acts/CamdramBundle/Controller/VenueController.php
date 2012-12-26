<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Venue;


/**
 * @RouteResource("Venue")
 */
class VenueController extends FOSRestController
{
    public function getAction($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Venue');
        $venue = $repo->findOneBySlug($slug);
        if (!$venue) {
        throw $this->createNotFoundException(
            'No venue found with the name '.$slug);
        }
        $view = $this->view($venue, 200)
            ->setTemplate("ActsCamdramBundle:Venue:show.html.twig")
            ->setTemplateVar('venue')
        ;
        
        return $view;
    }
}
