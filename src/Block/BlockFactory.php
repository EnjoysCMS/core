<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use DI\DependencyException;
use DI\FactoryInterface;
use DI\NotFoundException;

class BlockFactory
{
    public function __construct(private readonly FactoryInterface $factory)
    {
    }

    /**
     * @param class-string<AbstractBlock> $className
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function create(string $className): AbstractBlock
    {
        return $this->factory->make($className);
    }
}
