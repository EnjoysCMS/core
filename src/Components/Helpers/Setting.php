<?php

namespace EnjoysCMS\Core\Components\Helpers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;

/**
 * @deprecated
 */
class Setting extends HelpersBase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::$container->get('Setting');
        /** @var LoggerInterface $logger */
        $logger = self::$container->get(LoggerInterface::class);

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        $logger->debug(sprintf('Parameter `%s` not found! Return default value', $key));

        return $default;
    }
}
