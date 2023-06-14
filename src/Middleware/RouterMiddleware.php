<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Middleware;


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

final class RouterMiddleware implements MiddlewareInterface
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

        foreach ($match as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        $router = $this->router->getRouteCollection()->get($match['_route']);

        if ($router === null) {
            return $handler->handle($request);
        }


        $this->locations->setCurrentLocation($router);

        foreach (array_reverse($router->getOption('middlewares') ?? []) as $middleware){
            $handler->addToQueue($middleware);
        }
//dd($request->getAttributes());
//dd($handler);
        return $handler->handle($request);
    }

}
