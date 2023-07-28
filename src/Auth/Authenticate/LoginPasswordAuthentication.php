<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\Authenticate;


use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Auth\Identity;
use EnjoysCMS\Core\Auth\IdentityInterface;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Users\Entity\User;
use Psr\Http\Message\ServerRequestInterface;

final class LoginPasswordAuthentication implements Authentication
{

    private ?User $user = null;
    private string $loginField = 'login';
    private string $passwordField = 'password';

    public function __construct(
        private readonly UserStorageInterface $userStorage,
    ) {
    }

    public function authenticate(ServerRequestInterface $request): ?User
    {
        $login = $request->getParsedBody()[$this->loginField] ?? $request->getQueryParams()[$this->loginField] ?? '';
        $password = $request->getParsedBody()[$this->passwordField] ?? $request->getQueryParams()[$this->passwordField] ??  '';

        if ($this->validate($login, $password)){
            return $this->user;
        }

        return null;
    }

    private function validate(string $login, string $password): bool
    {
        /** @var User $user */
        $this->user = $this->userStorage->getUserByLogin($login);

        if ($this->user === null) {
            return false;
        }

        return password_verify($password, $this->user->getPasswordHash());
    }


    public function setPasswordField(string $passwordField): void
    {
        $this->passwordField = $passwordField;
    }

    public function setLoginField(string $loginField): void
    {
        $this->loginField = $loginField;
    }
}
