<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\AccessControl\Middleware;


use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Enjoys\Config\Config;
use EnjoysCMS\Core\AccessControl\AccessControl;
use EnjoysCMS\Core\Exception\ForbiddenException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Route;

final class AuthorizeMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly AccessControl $accessControl
    ) {
    }

    /**
     * @throws ForbiddenException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var null|Route $route */
        $route = $request->getAttribute('_route');
        /** @var null|string $routeName */
        $routeName = $request->getAttribute('_routeName');
        if ($route === null) {
            throw new InvalidArgumentException('Route not set');
        }
        if ($routeName === null) {
            throw new InvalidArgumentException('Route Name not set');
        }

        if (($route->getOption('acl') ?? true) !== false
            && !$this->accessControl->isAccess($routeName)
            && !$this->config->get('security->accessControl->disableChecking', false)
        ) {
            throw new ForbiddenException();
        }
        return $handler->handle($request);
    }
}
