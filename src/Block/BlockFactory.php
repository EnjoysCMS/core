<?php

namespace EnjoysCMS\Core\Block;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

class BlockFactory
{
    public function __construct(private Container $container)
    {
    }

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(string $className)
    {
        return $this->container->make($className);
    }
}
