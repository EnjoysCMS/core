<?php


namespace EnjoysCMS\Core\Components\Widgets;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\Widgets\Widgets;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetsTwigExtension extends AbstractExtension
{

    private \EnjoysCMS\Core\Components\Widgets\Widgets $widgets;

    public function __construct(EntityManager $entityManager, Environment $twig, LoggerInterface $logger = null)
    {
        $this->widgets = new Widgets($entityManager, $twig, $logger);
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
