<?php

declare(strict_types=1);


namespace EnjoysCMS\Core;


use HttpSoft\Message\Response;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class BaseController
{
    public function __construct(ResponseInterface $response = null)
    {
        $this->response = $response ?? new Response();
    }

    private function writeBody(string $body)
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
