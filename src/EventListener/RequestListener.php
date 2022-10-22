<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class RequestListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }

        $response = $event->getResponse();

        // Set multiple headers simultaneously
        $response->headers->add([
            'Header-Name1' => 'value',
            'Header-Name2' => 'ExampleValue',
        ]);

        // Or set a single header
        $response->headers->set("Example-Header", "ExampleValue");
    }
}
