<?php

namespace Acts\CamdramApiBundle\View;

use Acts\CamdramApiBundle\Configuration\AnnotationReader;
use Acts\CamdramApiBundle\Service\EntityUrlGenerator;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Laminas\Feed\Writer\Feed;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    public function createResponse(ViewHandler $handler, View $view, Request $request): Response
    {
        $feed = new Feed();

        $data = $view->getData();
        if (!is_array($data)) throw new UnsupportedMediaTypeHttpException("There is no RSS feed here.");
        if (empty($data)) {
            $feed->setTitle('Camdram Feed');
            $feed->setDescription('This feed from Camdram is currently empty.');
            $feed->setLink($this->urlGen->getDefaultUrl());
            goto respond;
        }

        $item = $data[array_key_first($data)];
        $feedData = $this->reader->read($item)->getFeed();
        if (!$feedData) throw new UnsupportedMediaTypeHttpException("There is no RSS feed here.");

        $class = get_class($item);
        $feed->setTitle($feedData->getName());
        $feed->setDescription($feedData->getDescription());
        $feed->setLink($this->urlGen->generateCollectionUrl($class));
        $feed->setFeedLink($this->urlGen->generateCollectionUrl($class, $request->getRequestFormat()), $request->getRequestFormat());

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

        respond:
        return new Response($feed->export($request->getRequestFormat()), 200, $view->getHeaders());
    }
}
