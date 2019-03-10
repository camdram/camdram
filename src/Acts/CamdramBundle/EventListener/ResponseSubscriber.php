<?php

namespace Acts\CamdramBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpCache\HttpCache;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    private $policy = null;
    private $header_name = "Content-Security-Policy";

    /** @inheritdoc */
    public static function getSubscribedEvents()
    {
        return [ KernelEvents::RESPONSE => 'onResponse' ];
    }

    /**
     * Callback function for event subscriber
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
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
        $script_hashes = file_get_contents(__DIR__ . '/../../../../web/build/csp_hashes.txt');
        // The global $kernel can be an actual kernel, a cache object, or undefined.
        $kernel = $GLOBALS['kernel'] ?? null;
        if ($kernel instanceof HttpCache) {
            $kernel = $kernel->getKernel();
        }
        $onProd = $kernel != null ? ($kernel->getEnvironment() == 'prod') : false;
        if ($script_hashes == false) {
            trigger_error('csp_hashes.txt does not exist, run ./gen_csp_hashes.py!');
        } else {
            $this->policy = "block-all-mixed-content; " .
            "default-src 'self' https://www.google-analytics.com https://*.openstreetmap.org " .
            "*.googleusercontent.com *.facebook.com *.googletagmanager.com; " .
            "font-src 'self' https://netdna.bootstrapcdn.com; " .
            "child-src *.google.com; " . # We don't put frames around our own content
            "frame-src *.google.com; " . # We don't put frames around our own content
            "script-src 'self' https://www.googletagmanager.com https://ajax.googleapis.com https://www.google-analytics.com *.google.com *.gstatic.com ".
            $script_hashes .
            "; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://netdna.bootstrapcdn.com";
            // We risk hitting rate limits if we send off literally all reports
            // to Sentry; censoring a random 2/3 of them is safer.
            if ($onProd && rand(0, 2) == 2) {
                $this->policy .= "; report-uri https://sentry.io/api/1273126/security/?sentry_key=303e272fab60425da4073a20d5a6c710";
                // Trial worked, setting the policy to enforce.
                // $this->header_name = "Content-Security-Policy-Report-Only";
            }
        }
    }
}
