<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Helpers\Redirect;


use Psr\Http\Message\ResponseInterface;

interface RedirectInterface
{
    public function http(string $uri = null, int $code = 302): ResponseInterface;
}
