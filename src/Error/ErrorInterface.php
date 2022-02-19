<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Error;


interface ErrorInterface
{
    public function http(int $code, string $message = null);

}