<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Blocks;

use DI\DependencyException;
use DI\FactoryInterface;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @deprecated
 * @todo Move to \EnjoysCMS\Core\Components\Extensions\Twig\CoreTwigExtension
 */
class BlocksTwigExtension extends AbstractExtension
{
    /**
     * @var Blocks
     */
    private Blocks $blocks;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
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

    /**
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function viewBlock(int|string $id): ?string
    {
        return $this->blocks->getBlock($id);
    }
}
