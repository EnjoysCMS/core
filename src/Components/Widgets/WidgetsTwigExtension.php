<?php


namespace EnjoysCMS\Core\Components\Widgets;


use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetsTwigExtension extends AbstractExtension
{

    private Widgets $widgets;

    public function __construct(ContainerInterface $container)
    {
        $this->widgets = $container->get(Widgets::class);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ViewWidget', [$this, 'viewWidget'], ['is_safe' => ['html']]),
        ];
    }

    public function viewWidget(int $id):? string
    {
        return $this->widgets->getWidget($id);
    }
}
