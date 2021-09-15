<?php

namespace App\EventListeners;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use App\EventListeners\RequestListener;

class ResponseListener
{
    public function __construct(
        private RequestListener $requestListener,
    )
    {
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        /** @var Request */
        $request = $event->getRequest();
        $response = $event->getResponse();
        $response->headers->set('Access-Control-Allow-Headers', 'X-Header-One,X-Header-Two');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        if ($request->getMethod() === Request::METHOD_OPTIONS) {
            $requestPathInfo = $request->getPathInfo();
            $optionResponse = $this->requestListener->handleOptions($requestPathInfo);
            $optionResponse->headers->set('Access-Control-Allow-Headers', 'X-Header-One,X-Header-Two');
            $optionResponse->headers->set('Access-Control-Allow-Origin', '*');
            $event->setResponse($optionResponse);
        }

//        $response = $event->getResponse();
//        $response->headers->set('Access-Control-Allow-Origin', '*');
//        $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT');
//        $response->headers->set('Access-Control-Allow-Headers', 'X-Header-One,X-Header-Two');
    }
}