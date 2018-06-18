<?php

namespace Acts\CamdramBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Acts\CamdramBundle\Entity\TermDate;

/**
 */
class TermDateController extends FOSRestController
{
    public function getTermAction($id)
    {
        $term = $this->getDoctrine()->getRepository('ActsCamdramBundle:TermDate')
                      ->find($id);
        $view = $this->view($term, 200)
            ->setTemplate('ActsCamdramBundle:TermDate:index.html.twig')
            ->setTemplateVar('term')
        ;

        return $view;
    }
}
