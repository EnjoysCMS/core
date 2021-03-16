<?php


namespace App\Components\Blocks;


use Doctrine\ORM\EntityManager;
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

    public function __construct(EntityManager $entityManager, Environment $twig, LoggerInterface $logger = null)
    {
        $this->blocks = new Blocks($entityManager, $twig, $logger);
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