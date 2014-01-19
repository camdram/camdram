<?php

namespace Acts\CamdramApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ActsCamdramApiBundle:Default:index.html.twig', array());
    }

    public function testConsoleAction()
    {
        return $this->render('ActsCamdramApiBundle:Default:test_console.html.twig', array());
    }
}
