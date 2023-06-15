<?php

namespace EnjoysCMS\Core\Location\Middleware;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\Location\Location;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Route;

class SetCurrentLocationMiddleware implements MiddlewareInterface
{

    public function __construct(private readonly Location $location,)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('_route');
        if ($route !== null) {
            $this->location->setCurrentLocation($route);
        }
        return $handler->handle($request);
    }
}
