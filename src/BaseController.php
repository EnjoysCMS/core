<?php

declare(strict_types=1);


namespace EnjoysCMS\Core;


use Psr\Http\Message\ResponseInterface;

abstract class BaseController
{
    public function __construct(protected ResponseInterface $response)
    {
    }

    protected function responseText(string $body = ''): ResponseInterface
    {
        $this->response->getBody()->write($body);
        return $this->response;
    }
}
