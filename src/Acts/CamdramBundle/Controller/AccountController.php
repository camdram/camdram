<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{
    public function facebookAction()
    {
 //       return $this->render('ActsCamdramBundle:Default:index.html.twig');
        return new Response();
    }
}
