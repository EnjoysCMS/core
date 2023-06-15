<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Routing\Middleware;


use EnjoysCMS\Core\Http\Response\RedirectInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RedirectToRouteByQueryStringMiddleware implements MiddlewareInterface
{

    private string $indexRouteName = 'system/index';

    public function __construct(
        private readonly RedirectInterface $redirect,
        private readonly RouteCollection $routeCollection,
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('_route');

        if ($route === $this->routeCollection->get($this->indexRouteName) && isset($request->getQueryParams()['_route'])) {
            $params = $request->getQueryParams();
            $redirectRouteName = (string)$request->getQueryParams()['_route'];
            unset($params['_route']);
            try {
                return $this->redirect->toRoute($redirectRouteName, $params);
            } catch (RouteNotFoundException) {
            }
        }
        return $handler->handle($request);
    }

    public function setIndexRouteName(string $indexRouteName): RedirectToRouteByQueryStringMiddleware
    {
        $this->indexRouteName = $indexRouteName;
        return $this;
    }
}
