<?php


namespace EnjoysCMS\Core\Components\Helpers;


class Error
{
    public static function code(int $code, string $message = null)
    {
        $error = new \EnjoysCMS\Core\Error\Error();
        $error->http($code, $message);
    }

}
