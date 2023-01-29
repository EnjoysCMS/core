<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Psr\Container\ContainerInterface;

/**
 * @deprecated
 */
class HelpersBase
{
    protected static ?ContainerInterface $container = null;

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }
}
