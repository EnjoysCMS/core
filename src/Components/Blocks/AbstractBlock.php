<?php


namespace EnjoysCMS\Core\Components\Blocks;


use Enjoys\Traits\Options;
use EnjoysCMS\Core\Entities\Block as Entity;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractBlock implements BlocksInterface
{

    use Options;

    public function __construct(protected Entity $block)
    {
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
