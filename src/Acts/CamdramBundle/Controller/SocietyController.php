<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;


/**
 * @RouteResource("Society")
 */
class SocietyController extends FOSRestController
{

    public function getAction($id)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Society');
        $society = $repo->findOneByShortName($id);
        if (!$society) {
        throw $this->createNotFoundException(
            'No society found with the name '.$society);
        }
        $view = $this->view($society, 200)
            ->setTemplate("ActsCamdramBundle:Society:index.html.twig")
            ->setTemplateVar('society')
        ;
        
        return $view;
    }
}
