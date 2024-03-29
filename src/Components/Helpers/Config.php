<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Helpers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @deprecated
 */
class Config extends HelpersBase
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get(string $section, string $key, $default = null)
    {
        if (null === $config = self::$container?->get(\Enjoys\Config\Config::class)->getConfig($section)) {
            return $default;
        }

        if (array_key_exists($key, $config)) {
            return $config[$key];
        }

        return $default;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getAll(string $section)
    {
        return self::$container?->get(\Enjoys\Config\Config::class)->getConfig($section) ?? [];
    }
}
