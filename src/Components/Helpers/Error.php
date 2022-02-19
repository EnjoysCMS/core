<?php


namespace EnjoysCMS\Core\Components\Helpers;


use EnjoysCMS\Core\Error\ErrorInterface;

class Error extends HelpersBase
{
    public static function code(int $code, string $message = null)
    {
        $error = self::getContainer()->get(ErrorInterface::class);
        $error->http($code, $message);
    }

}
