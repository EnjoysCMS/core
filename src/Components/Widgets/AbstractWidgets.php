<?php


namespace EnjoysCMS\Core\Components\Widgets;


use EnjoysCMS\Core\Entities\Widgets as Entity;
use Enjoys\Traits\Options;
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

    public function __construct(Environment $twig, Entity $widget)
    {
        $this->twig = $twig;
        $this->widget = $widget;

       // $this->setOptions($this->widget->getOptionsKeyValue());
    }


    abstract public function view();

    public static function getMeta(): ?array
    {
        return null;
    }
}
