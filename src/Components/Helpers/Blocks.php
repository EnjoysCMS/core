<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Doctrine\ORM\EntityManager;

class Blocks extends HelpersBase
{

    public static function getActiveBlocksController(): array
    {
        $ret = [];
        $blocks = self::$container->get(EntityManager::class)->getRepository(\EnjoysCMS\Core\Entities\Blocks::class)->findAll();
        /**
* 
         *
 * @var \EnjoysCMS\Core\Entities\Blocks $block 
*/
        foreach ($blocks as $block) {
            $ret[] = $block->getBlockActionAcl();
        }
        return $ret;
    }

}
