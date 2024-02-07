<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use EnjoysCMS\Core\Auth\AuthenticationStorage\PhpSession;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Identity implements IdentityInterface
{

    private ?User $user = null;
    private AuthenticationStorageInterface $authenticationStorage;


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly UserStorageInterface $userStorage,
        AuthenticationStorageInterface $authenticationStorage = null
    ) {
        $this->authenticationStorage = $authenticationStorage ?? $this->setAuthenticationStorage(PhpSession::class);
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setAuthenticationStorage(string|AuthenticationStorageInterface $authenticationStorage
    ): AuthenticationStorageInterface {
        if (is_string($authenticationStorage)) {
            $this->authenticationStorage = $this->container->get($authenticationStorage);
            return $this->authenticationStorage;
        }
        $this->authenticationStorage = $authenticationStorage;
        return $this->authenticationStorage;
    }

    public function setVerified(?User $user, array $payload = []): void
    {
        $this->user = $user;
        if ($user !== null) {
            $this->authenticationStorage->setVerified($user, $payload);
        }
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        $user = $this->user
            ?? $this->userStorage->getUser($this->authenticationStorage->getUserId())
            ?? $this->userStorage->getGuestUser()
            ?? throw new Exception(
                'Invalid user'
            );

        if ($user->isGuest()) {
            $this->authenticationStorage->logout();
        }
        return $user;
    }

    /**
     * @return AuthenticationStorageInterface
     */
    public function getAuthenticationStorage(): AuthenticationStorageInterface
    {
        return $this->authenticationStorage;
    }


}
