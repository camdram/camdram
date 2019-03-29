<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * April Fools day controller
 */
class FoolsController extends Controller
{
    /**
     * @Route("/dos-view", name="april_fools")
     */
    public function indexAction()
    {
        return $this->render('dos-view.html.twig');
    }
}
