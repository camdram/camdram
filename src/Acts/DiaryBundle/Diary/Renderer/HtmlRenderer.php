<?php
namespace Acts\DiaryBundle\Diary\Renderer;

use Acts\DiaryBundle\Diary\Diary;
use Symfony\Component\HttpFoundation\Response;

class HtmlRenderer
{
    /**
     * @var \Symfony\Bridge\Twig\TwigEngine
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(Diary $diary)
    {
        $view = $diary->createView();
        $html = $this->twig->render('ActsDiaryBundle:Diary:index.html.twig', array('diary' => $view));
        return new Response($html);
    }

}