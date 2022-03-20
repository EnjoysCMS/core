<?php

declare(strict_types=1);


namespace EnjoysCMS\Core;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class BaseController
{
    public function __construct(protected ResponseInterface $response)
    {
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


    private function writeBody(string $body)
    {
        $this->response->getBody()->write($body);
    }
}
