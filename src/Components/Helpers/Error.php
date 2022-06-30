<?php


namespace EnjoysCMS\Core\Components\Helpers;


use EnjoysCMS\Core\Error\ErrorInterface;

/**
 * @deprecated In version 4.5 will be removed. Throw EnjoysCMS\Core\Exception and use package enjoyscms/error-handler
 */
class Error extends HelpersBase
{

    public static function code(int $code, string $message = null)
    {
        trigger_deprecation(
            'enjoyscms/core',
            '4.3.1',
            'In version 4.5 will be removed. Throw EnjoysCMS\Core\Exception and use package enjoyscms/error-handler'
        );


        $error = self::getContainer()->get(ErrorInterface::class);
        $error->http($code, $message);
    }

}
