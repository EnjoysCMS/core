<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

class BlockFactory
{
    public function __construct(private readonly Container $container)
    {
    }

    /**
     * @param class-string<AbstractBlock> $className
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(string $className): AbstractBlock
    {
        return $this->container->make($className);
    }
}
