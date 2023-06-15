<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Routing\Middleware;


use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\Detector\Locations;
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
        private readonly Locations $locations,
    ) {
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $bridge = new HttpFoundationFactory();
        $match = $this->router->matchRequest($bridge->createRequest($request->withUploadedFiles([])));
        $route = $this->router->getRouteCollection()->get($match['_route']);
        $request = $request->withAttribute('_route', $route);
        return $handler->handle($request);
//        foreach ($match as $key => $value) {
//            $request = $request->withAttribute($key, $value);
//        }

//
//        dd($request->getAttributes());
//        if ($route === null) {
//            return $handler->handle($request);
//        }
//
//
//        $this->locations->setCurrentLocation($router);

//        foreach (array_reverse($router->getOption('middlewares') ?? []) as $middleware){
//            $handler->addToQueue($middleware);
//        }
//dd($request->getAttributes());
//dd($handler);

    }

}
