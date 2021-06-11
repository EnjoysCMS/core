<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Psr\Log\LoggerInterface;

class Setting extends HelpersBase
{
    public static function get(string $key, $default = null)
    {
        $settings = self::$container->get('Setting');
        /** @var LoggerInterface $logger */
        $logger = self::$container->get(LoggerInterface::class)->withName('Setting');

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        $logger->debug(sprintf('Parameter `%s` not found! Return default value', $key));

        return $default;
    }
}
