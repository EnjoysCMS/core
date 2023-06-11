<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Middleware;


use EnjoysCMS\Core\Detector\Locations;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Router;

final class RouterMiddleware implements MiddlewareInterface
{

    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        /** @var Router $router */
        $router = $this->container->get(Router::class);
        $bridge = new HttpFoundationFactory();
        $match = $router->matchRequest($bridge->createRequest($request->withUploadedFiles([])));
        foreach ($match as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $this->container->get(Locations::class)->setCurrentLocation($router->getRouteCollection()->get($match['_route']));

        return $handler->handle($request);
    }
}
