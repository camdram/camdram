<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function settingsAction()
    {
        return $this->render('ActsCamdramBundle:Account:settings.html.twig');
    }

    public function loginAction()
    {
        return $this->render('ActsCamdramBundle:Account:login.html.twig');
    }
}
