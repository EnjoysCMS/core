<?php


namespace App\Components\Helpers;


use App\Blocks\Custom;
use Doctrine\ORM\EntityManager;

class Blocks extends HelpersBase
{

    public static function getActiveBlocksController(): array
    {
        $ret = [];
        $blocks = self::$container->get(EntityManager::class)->getRepository(\App\Entities\Blocks::class)->findAll();
        /** @var \App\Entities\Blocks $block */
        foreach ($blocks as $block) {
            $ret[] = $block->getBlockActionAcl();
        }
        return $ret;
    }

}
