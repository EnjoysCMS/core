<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Entities\Block;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @deprecated
 */
class Blocks extends HelpersBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getActiveBlocksController(): array
    {
        /** @var Block[] $blocks */
        $blocks = self::$container->get(EntityManager::class)->getRepository(Block::class)->findAll();

        $ret = [];
        foreach ($blocks as $block) {
            $ret[] = $block->getBlockActionAcl();
        }
        return $ret;
    }
}
