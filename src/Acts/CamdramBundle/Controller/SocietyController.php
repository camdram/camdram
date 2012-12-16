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
<<<<<<< HEAD
        $society = $repo->findOneById($id);
        
        $view = $this->view($society, 200)
=======
        $show = $repo->findOneById($id);
        
        $view = $this->view($show, 200)
>>>>>>> d85b8092a83851489d0ef21e548f352ee862298a
            ->setTemplate("ActsCamdramBundle:Society:index.html.twig")
            ->setTemplateVar('society')
        ;
        
        return $view;
    }
}
