<?php

namespace Acts\CamdramApiBundle\View;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Zend\Feed\Writer\Feed;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Acts\CamdramApiBundle\Service\EntityUrlGenerator;
use Acts\CamdramApiBundle\Configuration\AnnotationReader;
use Acts\CamdramApiBundle\Exception\UnsupportedTypeException;

class FeedViewHandler
{
    private $reader;

    private $twig;

    private $urlGen;

    private $authorAddress;

    public function __construct(AnnotationReader $reader, \Twig\Environment $twig, EntityUrlGenerator $urlGen, $adminEmail)
    {
        $this->reader = $reader;
        $this->twig = $twig;
        $this->urlGen = $urlGen;
        $this->authorAddress = $adminEmail;
    }

    /**
     * Converts the viewdata to a RSS feed. Modify to suit your datastructure.
     *
     * @return Response
     */
    public function createResponse(ViewHandler $handler, View $view, Request $request)
    {
        try {
            $content = $this->createFeed($view, $request);
            $code = Response::HTTP_OK;
        } catch (UnsupportedTypeException $e) {
            $content = 'Unsupported entity';
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

        $annotationData = $this->reader->read($item);

        if ($item && $feedData = $annotationData->getFeed()) {
            $class = get_class($item);
            $feed->setTitle($feedData->getName());
            $feed->setDescription($feedData->getDescription());
            $feed->setLink($this->urlGen->generateCollectionUrl($class));
            $feed->setFeedLink($this->urlGen->generateCollectionUrl($class, $request->getRequestFormat()), $request->getRequestFormat());
        } else {
            $feed->setTitle('Camdram feed');
            $feed->setDescription('Camdram feed');
            $feed->setLink($this->urlGen->getDefaultUrl());
        }

        $lastModified = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        // Add one or more entries. Note that entries must be manually added once created.
        foreach ($data as $document) {
            $entry = $feed->createEntry();

            $entry->setTitle($accessor->getValue($document, $feedData->getTitleField()));
            $entry->setLink($this->urlGen->generateUrl($document));
            $entry->setDescription($this->twig->render($feedData->getTemplate(), array('entity' => $document)));

            if ($accessor->isReadable($document, $feedData->getUpdatedAtField())) {
                $entry->setDateModified($accessor->getValue($document, $feedData->getUpdatedAtField()));
            }

            $feed->addEntry($entry);

            if (!$lastModified || $entry->getDateModified() > $lastModified) {
                $lastModified = $entry->getDateModified();
            }
        }

        $feed->setDateModified($lastModified);

        return $feed->export($request->getRequestFormat());
    }
}
