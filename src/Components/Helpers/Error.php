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
        @trigger_error(
            'In version 4.5 will be removed. Throw EnjoysCMS\Core\Exception and use package enjoyscms/error-handler',
            E_USER_DEPRECATED
        );

        $error = self::getContainer()->get(ErrorInterface::class);
        $error->http($code, $message);
    }

}
