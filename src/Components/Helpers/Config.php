<?php
declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Helpers;

/**
 * Class Config
 * @package App\Components\Helpers
 */
class Config extends HelpersBase
{

    /**
     * @param string $section
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    static public function get(string $section, string $key, $default = null)
    {
        if (null === $config = self::$container->get('Config')->getConfig($section)){
            return  $default;
        }

        if(array_key_exists($key, $config)){
            return $config[$key];
        }

        return $default;
    }
}
