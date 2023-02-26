<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Interfaces;


use Psr\Http\Message\ResponseInterface;

interface RedirectInterface
{
    /**
     * @template TEmit of bool
     * @psalm-param TEmit $emit
     * @psalm-return (TEmit is true ? never-return : ResponseInterface)
     */
    public function http(string $uri = null, int $code = 302, bool $emit = false): ResponseInterface;
}
