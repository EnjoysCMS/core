<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Middleware;


use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Enjoys\Config\Config;
use EnjoysCMS\Core\AccessControl\ACL;
use EnjoysCMS\Core\Exception\ForbiddenException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class AclMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly ACL $acl
    ) {
    }

    /**
     * @throws ForbiddenException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('_route');
        if ($route === null) {
            throw new InvalidArgumentException('Route not set');
        }
        $controller = implode('::', (array)$route->getDefault('_controller'));

        if ($route->getOption('acl') !== false
            && !$this->acl->access($controller, (string)($route->getOption('comment') ?? $controller))
            && !$this->config->get('acl->disableChecking', false)
        ) {
            throw new ForbiddenException();
        }
        return $handler->handle($request);
    }
}
