<?php

namespace EnjoysCMS\Core\Widgets;

use Enjoys\Traits\Options;
use EnjoysCMS\Core\Entities\Widget as Entity;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Environment;

abstract class AbstractWidgets
{
    use Options;

    protected Environment $twig;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly ContainerInterface $container,
        protected Entity $widget
    ) {
        $this->twig = $container->get(Environment::class);
    }


    abstract public function view();

    public static function getMeta(): ?array
    {
        return null;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
