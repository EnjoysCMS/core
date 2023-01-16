<?php

namespace EnjoysCMS\Core\Components\Helpers;

class ACL extends HelpersBase
{
    public static function access($action, $comment = '')
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->access($action, $comment);
    }


    public static function getAcl($action)
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->getAcl($action);
    }

    public static function registerAcl($action, $comment = '')
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->addAcl($action, $comment);
    }
}
