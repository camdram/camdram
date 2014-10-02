<?php
namespace Acts\CamdramApiBundle\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;

use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Diary\Renderer\ICalRenderer;

class ICalViewHandler
{

    public function __construct()
    {

    }

    /**
     * Converts the viewdata to a RSS feed. Modify to suit your datastructure.
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request)
    {
        if ($view->getData() instanceof Diary) {
            return new Response($this->createFeed($view->getData()), Response::HTTP_OK, $view->getHeaders());
        }
        else {
            return new Response("Unsupported entity type", Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param $data array
     * @param format string, either rss or atom
     */
    protected function createFeed(Diary $diary)
    {
        $renderer = new ICalRenderer();
        return $renderer->render($diary);
    }
}
