<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Middleware;


use EnjoysCMS\Core\Interfaces\RedirectInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RedirectToRouteByQueryStringMiddleware implements MiddlewareInterface
{

    private string $indexRouteName = 'system/index';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RedirectInterface $redirect
    ) {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getAttribute('_route') === $this->indexRouteName && isset($request->getQueryParams()['_route'])) {
            $params = $request->getQueryParams();
            $route = (string)$request->getQueryParams()['_route'];
            unset($params['_route']);
            try {
                return $this->redirect->http($this->urlGenerator->generate($route, $params));
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
