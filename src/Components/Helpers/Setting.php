<?php


namespace EnjoysCMS\Core\Components\Helpers;


class Setting extends HelpersBase
{
    public static function get(string $key, $default = null)
    {
        $settings = self::$container->get('Setting');

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return $default;
    }
}
