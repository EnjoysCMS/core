<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Helpers\Redirect;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Redirect implements RedirectInterface
{
    public function __construct(private ServerRequestInterface $request, private ResponseInterface $response)
    {
    }

    public function http(string $uri = null, int $code = 302): ResponseInterface
    {
        return $this->response->withStatus($code)->withHeader(
            'Location',
            $uri ?? $this->request->getUri()->__toString()
        );
    }

}
