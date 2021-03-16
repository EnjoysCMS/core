<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Psr\Container\ContainerInterface;

class HelpersBase
{
    protected static ?ContainerInterface $container = null;

    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }
}
