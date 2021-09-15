<?php

namespace App\EventListeners;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class RequestListener extends UrlMatcher
{

    public function __construct(private RouterInterface $router)
    {
        $routesCollection = $this->router->getRouteCollection();
        $request = new Request();
        $request->setMethod("NO_METHOD");
        $requestContext = new RequestContext();
        $requestContext->fromRequest($request);
        parent::__construct($routesCollection, $requestContext);
    }

    public function handleOptions(string $path): Response
    {
        $this->match($path);
        $response = new Response();
        if (!empty($this->allow)) {
            $response->headers->set("Access-Control-Allow-Methods", implode(", ", array_unique($this->allow)));
            $response->setStatusCode(Response::HTTP_OK);
        } else {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }
        return $response;
    }

    public function match($pathinfo): array
    {
        $this->allow = [];
        $this->matchCollection(rawurldecode($pathinfo) ?: '/', $this->routes);
        if (!empty($this->allow)) {
            array_push($this->allow, Request::METHOD_OPTIONS);
        }
        return $this->allow;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if ($request->getMethod() === Request::METHOD_OPTIONS) {
//            $event->setResponse(new JsonResponse(['Options OK'], Response::HTTP_OK));
            $response = new Response();
            $event->setResponse($response);

        }
    }
}