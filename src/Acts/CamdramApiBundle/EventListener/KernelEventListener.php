<?php

namespace Acts\CamdramApiBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class KernelEventListener
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        /**
         * In this section of code we determine if an app is making a request
         * and, if it is, we increment its total request counter by one and
         * set the date of its last request to the current date & time.
         */
        $request = $event->getRequest();
        $params = $request->request;
        // The client_id POST parameter has the database primary key embedded
        $parts = explode("_", $params->get("client_id"));
        $clientSecret = $params->get("client_secret");
        if (count($parts) == 2) {
            $id = $parts[0];
            $clientId = $parts[1];
            if ($id && $clientId && $clientSecret) {
                $this->incrementAppRequestCounter($id, $clientId, $clientSecret);
            }
        }

        /**
         * In this section we attempt to prevent common programming libraries
         * and software development tools from making requests unless they are
         * using a unique User-Agent string. We allow all requests in testing
         * in order to make our lives easier.
         */
        if (getenv("SYMFONY_ENV") !== 'test') {
            $this->checkUserAgentHeader($event, $request);
        }
    }

    private function incrementAppRequestCounter($id, $clientId, $clientSecret) {
        $appRepo = $this->entityManager->getRepository('ActsCamdramApiBundle:ExternalApp');
        $app = $appRepo->findByCredentials($id, $clientId, $clientSecret);
        if ($app) {
            $now = new \DateTime;
            $app->incrementRequestCounter();
            $app->setLastUsed($now);
            $this->entityManager->flush();
        }
    }

    private function checkUserAgentHeader($event, $request) {
        $headers = $request->headers;
        $user_agent = $headers->get('User-Agent');
        $known_agents = array("AdobeAIR", "TALWinInetHTTPClient", "android-async-http", "Dalvik", "Anemone", "AngleSharp", "Apache-HttpClient", "Apache-HttpAsyncClient", "AHC", "axios", "BinGet", "CFNetwork", "Chilkat", "CsQuery", "cssutils", "curl", "libcurl", "EventMachine", "HttpClient", "Faraday", "Feed::Find", "Go-http-client", "http-client", "Go http package", "Goose", "got", "GStreamer", "souphttpsrc", "libsoup", "Guzzle", "GuzzleHttp", "hackney", "htmlayout", "http-kit", "HTTP_Request", "HTTP_Request2", "Indy Library", "Incutio", "Jakarta Commons-HttpClient", "Java", "libsoup", "libwww-perl", "lua-resty-http", "lwp-trivial", "LWP::Simple", "Manticore", "Mechanize", "Microsoft BITS", "Mojolicious", "okhttp", "PEAR", "HTTP_Request", "PECL::HTTP", "PHP-Curl-Class", "PHPCrawl", "Poe-Component-Client", "PycURL", "python-requests", "Python-urllib", "Python-webchecker", "longurl-r-package", "RestSharp", "RPT-HTTPClient", "eat", "Snoopy", "libsummer", "Symfony", "BrowserKit", "Typhoeus", "unirest-net", "urlgrabber", "WLMHttpTransport", "WinHttp", "WinInet", "WWW-Mechanize", "xine", "Zend_Http_Client");
        foreach ($known_agents as $agent_stub) {
            if (strpos($user_agent, $agent_stub) !== false) {
                $response = new Response();
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->setContent("Bad Request. You need to use a unique User-Agent string.");
                $event->setResponse($response);
            }
        }
    }
}
