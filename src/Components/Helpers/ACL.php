<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ACL extends HelpersBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function access($action, $comment = '')
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->access($action, $comment);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getAcl($action)
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->getAcl($action);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function registerAcl($action, $comment = '')
    {
        $acl = self::$container->get(\EnjoysCMS\Core\Components\AccessControl\ACL::class);
        return $acl->addAcl($action, $comment);
    }
}
