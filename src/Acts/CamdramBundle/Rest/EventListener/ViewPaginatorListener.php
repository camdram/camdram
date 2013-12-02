<?php
namespace Acts\CamdramBundle\Rest\EventListener;

use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Routing\RouterInterface;

use FOS\RestBundle\View\View;
use Pagerfanta\Pagerfanta;

use Acts\CamdramBundle\Rest\PaginatedCollection;
use Acts\CamdramBundle\Rest\ResponseQueryParams;
use Acts\CamdramBundle\Rest\ResponseUrls;

use Acts\DiaryBundle\Diary\Diary;
use Acts\DiaryBundle\Diary\Renderer\HtmlRenderer;

/**
 * Class ViewPaginatorListener
 *
 * This listener is called by Symfony after between calling the controller and sending the response to the browser.
 * It catches certain sorts of responses returned by controllers (for convenience) and converts them into a format
 * that the browser can render
 *
 * @package Acts\CamdramBundle\Rest\EventListener
 */

class ViewPaginatorListener
{

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Acts\DiaryBundle\Diary\Renderer\HtmlRenderer
     */
    private $diary_renderer;

    public function __construct(RouterInterface $router, HtmlRenderer $diary_renderer)
    {
        $this->router = $router;
        $this->diary_renderer = $diary_renderer;
    }

    /**
     * Detects a paginator returned by a Rest view and converts it into a PaginatorCollection. Also detects in a
     * Diary object is returned and sends it the DiaryBundle to be rendered.
     *
     * @param GetResponseForControllerResultEvent $event A GetResponseForControllerResultEvent instance
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $configuration = $request->attributes->get('_view');

        /** @var $view \FOS\RestBundle\View\View  */
        $view = $event->getControllerResult();
        if ($view instanceof View) {
            if ($view->getData() instanceOf Pagerfanta) {
                $paginator = $view->getData();

                if (!$request->get('page')) {
                    $request->query->set('page', 1);
                }
                if (!$request->get('limit')) {
                    $request->query->set('limit', $paginator->getMaxPerPage());
                }

                if (!$paginator->getMaxPerPage()) $paginator->setMaxPerPage($request->get('limit'));
                $paginator->setCurrentPage($request->get('page'));

                $url = $this->router->generate($request->get('_route'), array('_format' => $request->getRequestFormat('')), true);

                $view->setData(new PaginatedCollection($paginator, $request, $url));
            }
        }
    }
}
