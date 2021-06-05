<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Entities\Block;

class Blocks extends HelpersBase
{

    public static function getActiveBlocksController(): array
    {
        $ret = [];
        $blocks = self::$container->get(EntityManager::class)->getRepository(Block::class)->findAll();
        /**
* 
         *
 * @var Block $block
*/
        foreach ($blocks as $block) {
            $ret[] = $block->getBlockActionAcl();
        }
        return $ret;
    }

}
