<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Routing\Middleware;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Router;

final class RouteMatchMiddleware implements MiddlewareInterface
{

    public function __construct(
        private readonly Router $router,
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $bridge = new HttpFoundationFactory();
        $match = $this->router->matchRequest($bridge->createRequest($request->withUploadedFiles([])));
        $route = $this->router->getRouteCollection()->get($match['_route']);
        $request = $request->withAttribute('_route', $route);
        return $handler->handle($request);
    }

}
