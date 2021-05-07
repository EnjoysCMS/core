<?php


namespace EnjoysCMS\Core\Components\Blocks;


use EnjoysCMS\Core\Entities\Blocks as Entity;
use Enjoys\Traits\Options;
use Psr\Container\ContainerInterface;
use Twig\Environment;

abstract class AbstractBlock
{

    use Options;


    protected ContainerInterface $container;
    /**
     * @var Entity
     */
    protected Entity $block;

    public function __construct(ContainerInterface $container, Entity $block)
    {
        $this->container = $container;
        $this->block = $block;

        $this->setOptions($this->block->getOptionsKeyValue());
    }


    abstract public function view();

    public static function getMeta(): ?array
    {
        return null;
    }
}
