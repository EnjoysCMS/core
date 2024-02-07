<?php

namespace EnjoysCMS\Core\Extensions\Twig;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

class RuntimeLoader implements RuntimeLoaderInterface
{

    public function __construct(private readonly ContainerInterface $container)
    {
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function load(string $class)
    {
        return $this->container->get($class);
    }
}
