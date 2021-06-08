<?php


namespace EnjoysCMS\Core\Components\Widgets;


use EnjoysCMS\Core\Entities\Widget as Entity;
use Enjoys\Traits\Options;
use Psr\Container\ContainerInterface;
use Twig\Environment;

abstract class AbstractWidgets
{

    use Options;

    protected Environment $twig;

    public function __construct(private ContainerInterface $container, protected Entity $widget)
    {
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
