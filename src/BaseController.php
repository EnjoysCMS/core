<?php

declare(strict_types=1);

namespace EnjoysCMS\Core;

use Psr\Http\Message\ResponseInterface;

abstract class BaseController
{
    public function __construct(protected ResponseInterface $response)
    {
    }

    private function writeBody(string $body): void
    {
        $this->response->getBody()->write($body);
    }

    protected function responseText(string $body = ''): ResponseInterface
    {
        $this->writeBody($body);
        return $this->response;
    }

    protected function responseJson($data): ResponseInterface
    {
        $this->response = $this->response->withHeader('content-type', 'application/json');
        $this->writeBody(json_encode($data));
        return $this->response;
    }
}
