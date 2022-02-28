<?php

namespace Acts\CamdramBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    /** @var string */
    private $policy = null;
    /** @var string */
    private $header_name = "Content-Security-Policy";

    /** @inheritdoc */
    public static function getSubscribedEvents()
    {
        return [ KernelEvents::RESPONSE => 'onResponse' ];
    }

    /**
     * Callback function for event subscriber
     */
    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        if ($this->policy == null) {
            $this->generatePolicy();
        }

        # There is no point sending the X-* versions of this header as the
        # browsers in question (largely IE and Android browser) don't support
        # hashes in CSP.
        $response->headers->set($this->header_name, $this->policy);

        $response->headers->set('x-frame-options', 'deny');
    }

    private function generatePolicy()
    {
        $script_hashes = file_get_contents(__DIR__ . '/../../../../public/build/csp_hashes.txt');
        if ($script_hashes == false) {
            trigger_error('csp_hashes.txt does not exist, run ./gen_csp_hashes.py!');
        } else {
            $this->policy = "block-all-mixed-content; " .
            "default-src 'self' https://*.openstreetmap.org " .
            "*.googleusercontent.com *.facebook.com *.googletagmanager.com; " .
            "img-src 'self' https://*.openstreetmap.org " .
            "*.googleusercontent.com *.facebook.com *.googletagmanager.com " .
            "*.fbcdn.net *.fbsbx.com *.twimg.com; " .
            "font-src 'self' https://netdna.bootstrapcdn.com; " .
            "child-src *.google.com; " . # We don't put frames around our own content
            "frame-src *.google.com; " . # We don't put frames around our own content
            "script-src 'self' *.google.com *.gstatic.com ".
            $script_hashes .
            "; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://netdna.bootstrapcdn.com";
        }
    }
}
