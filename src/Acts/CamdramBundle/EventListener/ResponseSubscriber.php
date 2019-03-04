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
            "default-src 'self' https://www.google-analytics.com https://*.openstreetmap.org; " .
            "font-src 'self' https://netdna.bootstrapcdn.com; " .
            "script-src 'self' https://www.googletagmanager.com https://ajax.googleapis.com https://www.google-analytics.com ".
            $script_hashes .
            "; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://netdna.bootstrapcdn.com";
            if ($onProd) {
                $this->policy .= "; report-uri https://sentry.io/api/1273126/security/?sentry_key=303e272fab60425da4073a20d5a6c710";
                // As this is just a trial no content will actually be blocked
                // on production.
                $this->header_name = "Content-Security-Policy-Report-Only";
            }
        }
    }
}
