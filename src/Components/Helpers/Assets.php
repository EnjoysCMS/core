<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Enjoys\AssetsCollector;
use Psr\Log\LoggerInterface;

class Assets extends HelpersBase
{
    public static function css($paths, $ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    {
        $assets = self::$container->get(AssetsCollector\Assets::class);
        $assets->add('css', $paths, $ns);
    }

    public static function js($paths, $ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    {
        $assets = self::$container->get(AssetsCollector\Assets::class);
        $assets->add('js', $paths, $ns);
    }

    public static function createSymlink($link, $target)
    {
        $logger = self::$container->get(LoggerInterface::class)->withName('AssetsCollector');
        AssetsCollector\Helpers::createSymlink($link, $target, $logger);
    }

    //    public static function getCss($ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    //    {
    //        $assets = self::$container->get(AssetsCollector\Assets::class);
    //        $assets->getCss($ns);
    //    }
    //
    //
    //    public static function getJs($ns = AssetsCollector\Assets::NAMESPACE_COMMON)
    //    {
    //        $assets = self::$container->get(AssetsCollector\Assets::class);
    //        $assets->getJs($ns);
    //    }
}
