<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth\Strategy;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Enjoys\Config\Config;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Exception;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Auth\Authenticate;
use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Auth\AuthorizedData;
use EnjoysCMS\Core\Auth\StrategyInterface;
use EnjoysCMS\Core\Auth\TokenManage;
use EnjoysCMS\Core\Users\Entity\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PhpSession implements StrategyInterface
{

    private ?AuthorizedData $authData = null;

    public function __construct(
        private readonly Session $session,
        private readonly Cookie $cookie,
        private readonly Config $config,
        private readonly Authenticate\TokenAuthentication $tokenAuthentication,
        private readonly TokenManage $tokenManage,
        private readonly ServerRequestInterface $request,
    ) {
        $this->tokenName = $this->config->get('security->token_name') ?? '_token_refresh';
    }


    public function authorize(?User $user, array $data = []): void
    {
        if ($user === null) {
            $this->logout();
            return;
        }

        $this->session->set([
            'auth' => [
                'user' => [
                    'id' => $user->getId()
                ],
                'authenticate' => $data['authenticate'] ?? 'login'
            ]
        ]);
    }


    public function logout(): void
    {
        $this->session->delete('auth');
    }

    public function getAuthorizedData(): ?AuthorizedData
    {
        return $this->authData;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function isAuthorized($retry = 0, ?Authentication $authentication = null): bool
    {
        $this->authData = AuthorizedData::fromArray($this->session->get('auth', []));

        if ($this->authData->userId !== null) {
            return true;
        }

        $tokenName = $this->config->get('security->token_name') ?? '_token_refresh';
        $autologinToken = $this->cookie->get($tokenName);

        if ($autologinToken !== null && $retry < 1) {
            $retry++;
            $authentication = $authentication ?? $this->tokenAuthentication->withHeaderName('X-REFRESH-TOKEN');
            $user = $authentication->authenticate($this->request->withAddedHeader('X-REFRESH-TOKEN', $autologinToken));
            $this->authorize(
                $user,
                ['authenticate' => 'autologin']
            );
            if ($user !== null){
                $this->tokenManage->write($user, $autologinToken);
            }
            return $this->isAuthorized($retry, $authentication);
        }
        return false;
    }


}
