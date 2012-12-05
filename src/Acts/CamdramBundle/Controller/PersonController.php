<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Person;


/**
 * @RouteResource("Person")
 */
class PersonController extends FOSRestController
{

    public function getAction($id)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Person');
        $person = $repo->findOneById($id);
        
        $view = $this->view($person, 200)
            ->setTemplate("ActsCamdramBundle:Person:index.html.twig")
            ->setTemplateVar('person')
        ;
        
        return $view;
    }
}
