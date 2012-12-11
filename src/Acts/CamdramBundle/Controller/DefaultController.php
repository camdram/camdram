<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ActsCamdramBundle:Default:index.html.twig');
    }

    public function settingsAction()
    {
        return $this->render('ActsCamdramBundle:Default:index.html.twig');
    }

    public function loginAction()
    {
        return $this->render('ActsCamdramBundle:Default:login.html.twig');
    }
}
