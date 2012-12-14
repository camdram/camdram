<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Show;


/**
 * @RouteResource("Show")
 */
class ShowController extends FOSRestController
{

    public function getAction($id)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Show');
        $show = $repo->findOneById($id);

        $view = $this->view($show, 200)
            ->setTemplate("ActsCamdramBundle:Show:index.html.twig")
            ->setTemplateVar('show')
        ;
        
        return $view;
    }
}
