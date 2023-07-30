<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use DI\Container;
use EnjoysCMS\Core\Auth\AuthenticationStorage\PhpSession;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;

final class Identity implements IdentityInterface
{

    private ?User $user;
    private AuthenticationStorageInterface $authenticationStorage;


    public function __construct(
        private readonly Container $container,
        private readonly UserStorageInterface $userStorage,
        AuthenticationStorageInterface $authenticationStorage = null
    ) {
        $this->authenticationStorage = $authenticationStorage ?? $this->setAuthenticationStorage(PhpSession::class);
    }


    public function setAuthenticationStorage(string|AuthenticationStorageInterface $authenticationStorage
    ): AuthenticationStorageInterface {
        if (is_string($authenticationStorage)) {
            $this->authenticationStorage = $this->container->get($authenticationStorage);
            return $this->authenticationStorage;
        }
        $this->authenticationStorage = $authenticationStorage;
        return $this->authenticationStorage;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
        $this->authenticationStorage->setUser($user);
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
//        dd($this->authenticationStorage->getUserId());
        $this->user = $this->userStorage->getUser($this->authenticationStorage->getUserId());
        return $this->user ?? $this->userStorage->getGuestUser() ?? throw new Exception('Invalid user');
    }

    /**
     * @return AuthenticationStorageInterface
     */
    public function getAuthenticationStorage(): AuthenticationStorageInterface
    {
        return $this->authenticationStorage;
    }


}
