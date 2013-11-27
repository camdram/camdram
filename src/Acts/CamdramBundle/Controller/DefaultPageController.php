<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultPageController extends Controller
{
    public function pageAction($contentDocument)
    {
        return $this->render('ActsCamdramBundle:Page:index.html.twig', array('page' => $contentDocument,));
    }
}
