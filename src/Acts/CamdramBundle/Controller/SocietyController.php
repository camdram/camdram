<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Acts\CamdramBundle\Entity\Society;


/**
 */
class SocietyController extends FOSRestController
{

    public function getSocietiesAction()
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Society');
        $societies = $repo->findAllOrderedByCollegeName();

        $view = $this->view($societies, 200)
            ->setTemplate("ActsCamdramBundle:Society:index.html.twig")
            ->setTemplateVar('societies')
        ;
        
        return $view;
    }

    public function getSocietyAction($slug)
    {
        $repo = $this->getDoctrine()->getEntityManager()->getRepository('ActsCamdramBundle:Society');
        $society = $repo->findOneByShortName($slug);
        if (!$society) {
        throw $this->createNotFoundException(
            'No society found with the name '.$society);
        }
        $view = $this->view($society, 200)
            ->setTemplate("ActsCamdramBundle:Society:show.html.twig")
            ->setTemplateVar('society')
        ;
        
        return $view;
    }
}
