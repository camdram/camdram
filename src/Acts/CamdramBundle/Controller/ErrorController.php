<?php

namespace Acts\CamdramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function __invoke(Request $request, \Throwable $exception,
        \Symfony\Component\ErrorHandler\ErrorRenderer\SerializerErrorRenderer $errorRenderer)
    {
        $format = strtolower($request->getPreferredFormat());

        $exception = $errorRenderer->render($exception);
        if ($format === 'json') {
            $content = json_encode(['error' => [
                'code' => $exception->getStatusCode(),
                'message' => $exception->getStatusText()
            ]]);
        } else if ($format === 'xml') {
            $document = new \DOMDocument();
            $root = $document->createElement('error');
            $root->setAttribute('code', $exception->getStatusCode());
            $root->setAttribute('message', $exception->getStatusText());
            $document->appendChild($root);
            $content = $document->saveXML();
        } else {
            $content = $exception->getAsString();
        }

        return new Response($content, $exception->getStatusCode(), $exception->getHeaders());
    }
}
