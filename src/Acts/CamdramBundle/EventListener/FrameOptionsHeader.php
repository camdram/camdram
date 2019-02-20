<?php

namespace Acts\CamdramBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class FrameOptionsHeader {
    public function onKernelResponse(FilterResponseEvent $event) {
        $event->getResponse()->headers->set('x-frame-options', 'deny');
    }
}
