<?php
namespace Acts\CamdramApiBundle\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Zend\Feed\Writer\Feed;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;

use Acts\CamdramApiBundle\Service\EntityUrlGenerator;
use Acts\CamdramApiBundle\AnnotationReader\FeedAnnotationReader;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;

class FeedViewHandler
{
    private $reader;

    private $twig;

    private $urlGen;

    private $authorAddress;

    public function __construct(FeedAnnotationReader $reader, \Twig_Environment $twig, EntityUrlGenerator $urlGen, $authorAddress)
    {
        $this->reader = $reader;
        $this->twig = $twig;
        $this->urlGen = $urlGen;
        $this->authorAddress = $authorAddress;
    }

    /**
     * Converts the viewdata to a RSS feed. Modify to suit your datastructure.
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request)
    {
        try {
            $content = $this->createFeed($view, $request);
            $code = Response::HTTP_OK;
        } catch (UnsupportedTypeException $e) {
            $content = "Unsupported entity";
            $code = Response::HTTP_BAD_REQUEST;
        }

        return new Response($content, $code, $view->getHeaders());
    }

    /**
     * @param $data array
     * @param format string, either rss or atom
     */
    protected function createFeed(View $view, Request $request)
    {
        $feed = new Feed();

        $data = $view->getData();
        $item = current($data);
        if ($item && $feedData = $this->reader->read($item)) {
            $class = get_class($item);
            $feed->setTitle($feedData->getName());
            $feed->setDescription($feedData->getDescription());
            $feed->setLink($this->urlGen->generateCollectionUrl($class));
            $feed->setFeedLink($this->urlGen->generateCollectionUrl($class, $request->getRequestFormat()), $request->getRequestFormat());
        } else {
            $feed->setTitle('Camdram feed');
            $feed->setDescription('Camdram feed');
        }

        $lastModified = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        // Add one or more entries. Note that entries must be manually added once created.
        foreach ($data as $document) {
            $entry = $feed->createEntry();

            $entry->setTitle($accessor->getValue($document, $feedData->getTitleField()));
            $entry->setLink($this->urlGen->generateUrl($document));
            $entry->setDateCreated($accessor->getValue($document, $feedData->getCreatedAtField()));
            $entry->setDateModified($accessor->getValue($document, $feedData->getUpdatedAtField()));
            $entry->setDescription($this->twig->render($feedData->getTemplate(), array('entity' => $document)));

            $feed->addEntry($entry);

            if (!$lastModified || $entry->getDateModified() > $lastModified) {
                $lastModified = $entry->getDateModified();
            }
        }

        $feed->setDateModified($lastModified);

        return $feed->export($request->getRequestFormat());
    }
}
