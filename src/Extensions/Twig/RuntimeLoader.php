<?php

namespace EnjoysCMS\Core\Extensions\Twig;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class RuntimeLoader implements RuntimeLoaderInterface
{

    public function __construct(private readonly Container $container)
    {
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function load(string $class)
    {
        return $this->container->get($class);
    }
}
