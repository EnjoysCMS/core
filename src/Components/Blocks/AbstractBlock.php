<?php


namespace EnjoysCMS\Core\Components\Blocks;


use Enjoys\Traits\Options;
use EnjoysCMS\Core\Entities\Blocks as Entity;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractBlock implements BlocksInterface
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

    public static function getMeta(): array
    {
        return Yaml::parseFile(static::getBlockDefinitionFile())[static::class];
    }

    public function preRemove()
    {

    }

    public function postEdit(?Entity $oldBlock = null)
    {

    }

    public function postClone(?Entity $cloned = null)
    {

    }

}
