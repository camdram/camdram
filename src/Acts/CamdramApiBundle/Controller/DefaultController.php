<?php

namespace Acts\CamdramApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('api/default/index.html.twig', array());
    }

    public function testConsoleAction()
    {
        return $this->render('api/default/test_console.html.twig', array());
    }
}
