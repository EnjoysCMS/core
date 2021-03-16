<?php


namespace App\Components\Helpers;


class Modules extends HelpersBase
{
    public static function installed()
    {
        return self::$container->get('Modules');
    }
}
