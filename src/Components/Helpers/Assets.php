<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Enjoys\AssetsCollector;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated
 */
class Assets extends HelpersBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function css($paths, $ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    {
        $assets = self::$container->get(AssetsCollector\Assets::class);
        $assets->add('css', $paths, $ns);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function js($paths, $ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    {
        $assets = self::$container->get(AssetsCollector\Assets::class);
        $assets->add('js', $paths, $ns);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function createSymlink($link, $target)
    {
        $logger = self::$container->get(LoggerInterface::class);
        AssetsCollector\Helpers::createSymlink($link, $target, $logger);
    }
}
