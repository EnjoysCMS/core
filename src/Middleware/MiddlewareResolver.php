<?php

namespace EnjoysCMS\Core\Middleware;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MiddlewareResolver implements MiddlewareResolverInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @psalm-suppress MissingReturnType
     */
    public function resolve(mixed $entry): mixed
    {
        if (is_string($entry)) {
            $entry = $this->container->get($entry);
        }
        return $entry;
    }
}
