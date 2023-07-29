<?php

namespace EnjoysCMS\Core\Routing\Middleware;

use EnjoysCMS\Core\Middleware\HttpMiddlewareDispatcher;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Route;

class RouteDispatchMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly HttpMiddlewareDispatcher $middlewareDispatcher)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('_route');
        $routeMiddlewares = $route->getOption('middlewares') ?? [];

        if ($routeMiddlewares !== []){
            $this->middlewareDispatcher->addQueue($routeMiddlewares);
            return $this->middlewareDispatcher->handle($request);
        }

        return $handler->handle($request);
    }
}
