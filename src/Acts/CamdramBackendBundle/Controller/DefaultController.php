<?php

namespace Acts\CamdramBackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ActsCamdramBackendBundle:Default:index.html.twig', array('name' => $name));
    }
}
