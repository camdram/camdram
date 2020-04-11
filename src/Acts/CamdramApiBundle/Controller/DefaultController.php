<?php

namespace Acts\CamdramApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
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
