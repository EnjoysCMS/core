<?php


namespace EnjoysCMS\Core\Components\Blocks;


use DI\FactoryInterface;
use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BlocksTwigExtension extends AbstractExtension
{
    /**
     * @var Blocks
     */
    private Blocks $blocks;

    public function __construct(FactoryInterface $container)
    {
        $this->blocks = $container->make(Blocks::class);
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ViewBlock', [$this, 'viewBlock'], ['is_safe' => ['html']]),
        ];
    }

    public function viewBlock(int $id):? string
    {
        return $this->blocks->getBlock($id);
    }
}
