<?php


namespace EnjoysCMS\Core\Components\Widgets;


use EnjoysCMS\Core\Entities\Widget as Entity;
use Enjoys\Traits\Options;
use Psr\Container\ContainerInterface;
use Twig\Environment;

abstract class AbstractWidgets
{

    use Options;

    /**
     * @var Environment
     */
    protected Environment $twig;
    /**
     * @var Entity
     */
    protected Entity $widget;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, Entity $widget)
    {
        $this->twig = $container->get(Environment::class);
        $this->widget = $widget;

       // $this->setOptions($this->widget->getOptionsKeyValue());
        $this->container = $container;
    }


    abstract public function view();

    public static function getMeta(): ?array
    {
        return null;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
