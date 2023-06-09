<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Http\Response;


use Psr\Http\Message\ResponseInterface;

interface RedirectInterface
{
    /**
     * @template TEmit of bool
     * @psalm-param TEmit $emit
     * @psalm-return (TEmit is true ? never-return : ResponseInterface)
     */
    public function toUrl(string $uri = null, int $code = 302, bool $emit = false): ResponseInterface;

    /**
     * @template TEmit of bool
     * @psalm-param TEmit $emit
     * @psalm-return (TEmit is true ? never : ResponseInterface)
     */
    public function toRoute(string $routeName, array $params = [], int $code = 302, bool $emit = false): ResponseInterface;
}
