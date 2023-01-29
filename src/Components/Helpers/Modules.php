<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @deprecated
 */
class Modules extends HelpersBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function installed()
    {
        return self::$container->get('Modules');
    }
}
