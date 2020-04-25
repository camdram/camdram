<?php

namespace Acts\DiaryBundle\Diary\Renderer;

use Acts\DiaryBundle\Diary\Diary;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class HtmlRenderer
 *
 * Takes in a Diary object and outputs HTML
 */
class HtmlRenderer
{
    private $twig;

    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Gets a DiaryView, passes it to the Twig template and returns the rendered HTML as a Symfony Response object
     *
     * @param Diary $diary
     *
     * @return Response
     */
    public function render(Diary $diary)
    {
        $view = $diary->createView();

        return $this->twig->render('diary/index.html.twig', array('diary' => $view));
    }
}
