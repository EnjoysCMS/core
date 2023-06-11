<?php

namespace EnjoysCMS\Core\Widgets;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @deprecated
 * @todo Move to \EnjoysCMS\Core\Components\Extensions\Twig\CoreTwigExtension
 */
class WidgetsTwigExtension extends AbstractExtension
{
    private Widgets $widgets;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
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

    public function viewWidget(int $id): ?string
    {
        return $this->widgets->getWidget($id);
    }
}
