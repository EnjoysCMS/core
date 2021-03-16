<?php


namespace App\Components\Helpers;


class Error
{
    public static function code(int $code, string $message = null)
    {
        $error = new \App\Controller\Error();
        $error->http($code, $message);
    }

}
