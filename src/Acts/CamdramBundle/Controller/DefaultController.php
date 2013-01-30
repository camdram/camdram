<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $news_repo = $this->getDoctrine()->getRepository('ActsCamdramBundle:News');
        $news = $news_repo->getRecent(20);

        return $this->render('ActsCamdramBundle:Default:index.html.twig', array('news' => $news));
    }

    public function thisWeekAction()
    {
        $diary = $this->get('acts.diary');
        //$diary->addEvent($blah);
        return $diary;
    }

    public function nextWeekAction()
    {
        $diary = $this->get('acts.diary');
        //$diary->addEvent($blah);
        return $diary;
    }
}
