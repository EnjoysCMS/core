<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth\Middleware;

use App\System\Controller\Logout;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Auth\Authenticate\HttpBasicAuthentication;
use EnjoysCMS\Core\Auth\AuthenticationStorage\PhpSession;
use EnjoysCMS\Core\Auth\Identity;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class BasicAuthWithLogoutMiddleware implements MiddlewareInterface
{

    private string $realm = 'My realm';
    private ?string $logoutControllerClassName = null;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly HttpBasicAuthentication $authentication,
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly Session $session,
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
        if ($this->logoutControllerClassName !== null) {
            if ($request->getAttribute('_controller') === $this->logoutControllerClassName) {
                $this->session->set([
                    'www-authenticate-logged' => false
                ]);
                return $handler->handle($request);
            }
        }


        if ($this->identity->getUser()->isUser()) {
            return $handler->handle($request);
        }

        $user = $this->authentication->authenticate($request);

        if (!$this->session->get('www-authenticate-logged', false) || !$user) {
            $this->session->set([
                'www-authenticate-logged' => true
            ]);

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

    public function setLogoutControllerClassName(string $logoutControllerClassName): void
    {
        $this->logoutControllerClassName = $logoutControllerClassName;
    }

}
