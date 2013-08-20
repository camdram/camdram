<?php

namespace Acts\ExternalLoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ActsExternalLoginBundle:Default:index.html.twig', array('name' => $name));
    }
}
