<?php


namespace EnjoysCMS\Core\Components\AccessControl;


class Autologin
{

    public static function getTokenName()
    {
        return sprintf('app%u', crc32('autologin'));
    }
}