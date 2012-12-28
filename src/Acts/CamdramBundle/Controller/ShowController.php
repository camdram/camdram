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

    public function getAction($slug)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('ActsCamdramBundle:Show');
        $show = $repo->findOneBySlug($slug);

        if (!$show) {
            throw $this->createNotFoundException('That show does not exist');
        }
        
        $view = $this->view($show, 200)
            ->setTemplate("ActsCamdramBundle:Show:index.html.twig")
            ->setTemplateVar('show')
        ;
        
        return $view;
    }
 }
