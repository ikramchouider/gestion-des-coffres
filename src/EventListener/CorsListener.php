<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;

class CorsListener
{
    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $event->getRequest();


        if (strpos($request->getPathInfo(), '/api/') === 0) {
            $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
            $response->headers->set('Access-Control-Allow-Methods', 'GET', 'POST', 'PUT', 'PATCH', 'OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type', 'Authorization', 'Accept');
        }
    }
}
