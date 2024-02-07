<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth\Middleware;

use EnjoysCMS\Core\Auth\Authenticate\TokenAuthentication;
use EnjoysCMS\Core\Auth\AuthenticationStorage\Memory;
use EnjoysCMS\Core\Auth\Identity;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BearerAuthMiddleware implements MiddlewareInterface
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private TokenAuthentication $authentication,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Identity $identity,
    ) {
        $this->identity->setAuthenticationStorage(Memory::class);
        $this->authentication = $this->authentication
            ->withHeaderName('Authorization')
            ->withPattern('/^Bearer\s+(.*?)$/');
    }


    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->authentication->authenticate($request);

        if ($user === null) {
            $response = $this->responseFactory->createResponse(401);
            $response->getBody()->write(json_encode('401 Unauthorized'));
            return $response;
        }


        $this->identity->setVerified($user);

        return $handler->handle($request);
    }


}
