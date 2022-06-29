<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Error;

/**
 * @deprecated In version 4.5 will be removed. Throw EnjoysCMS\Core\Exception and use package enjoyscms/error-handler
 */
interface ErrorInterface
{
    public function http(int $code, string $message = null);

}
