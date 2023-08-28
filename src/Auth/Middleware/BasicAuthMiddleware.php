<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth\Middleware;

use EnjoysCMS\Core\Auth\Authenticate\HttpBasicAuthentication;
use EnjoysCMS\Core\Auth\AuthenticationStorage\PhpSession;
use EnjoysCMS\Core\Auth\Identity;
use Exception;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BasicAuthMiddleware implements MiddlewareInterface
{

    private string $realm = 'My realm';

    public function __construct(
        private readonly HttpBasicAuthentication $authentication,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Identity $identity,
    ) {
        $this->identity->setAuthenticationStorage(PhpSession::class);
    }


    public function withRealm(string $realm): self
    {
        $new = clone $this;
        $new->realm = $realm;
        return $new;
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->identity->getUser()->isUser()) {
            return $handler->handle($request);
        }

        $user = $this->authentication->authenticate($request);

        if (!$user) {
            $response = $this->responseFactory->createResponse(401)
                ->withHeader('WWW-Authenticate', sprintf('Basic realm="%s"', $this->realm));
            $response->getBody()->write('401 Unauthorized');

            return $response;
        }

        $this->identity->setVerified($user, ['authenticate' => 'basic-authenticate']);

        return $handler->handle($request)
            ->withStatus(302)
            ->withHeader(
                'Location',
                $request->getUri()->__toString()
            );
    }

}
