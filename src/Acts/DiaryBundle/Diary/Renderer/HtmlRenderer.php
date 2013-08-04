<?php
namespace Acts\DiaryBundle\Diary\Renderer;

use Acts\DiaryBundle\Diary\Diary;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HtmlRenderer
 *
 * Takes in a Diary object and outputs HTML
 *
 * @package Acts\DiaryBundle\Diary\Renderer
 */
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

    /**
     * Gets a DiaryView, passes it to the Twig template and returns the rendered HTML as a Symfony Response object
     *
     * @param Diary $diary
     * @return Response
     */
    public function render(Diary $diary)
    {
        $view = $diary->createView();
        $html = $this->twig->render('ActsDiaryBundle:Diary:index.html.twig', array('diary' => $view));
        return new Response($html);
    }

}