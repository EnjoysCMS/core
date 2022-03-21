<?php

declare(strict_types=1);


namespace EnjoysCMS\Core;


use HttpSoft\Message\Response;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

abstract class BaseController
{
    protected ServerRequestInterface $request;
    protected ResponseInterface $response;

    public function __construct(ResponseInterface $response = null, ServerRequestInterface $request = null)
    {
        $this->response = $response ?? new Response();
        $this->request = $request ?? ServerRequestCreator::createFromGlobals();

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
