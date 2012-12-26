<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsController extends Controller
{
    public function indexAction()
    {
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');
        $news = $news_repo->getRecent(50);

        return $this->render('ActsCamdramBundle:News:index.html.twig', array('news' => $news));
    }
}
