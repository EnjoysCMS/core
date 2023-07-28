<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\Authenticate;


use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Users\Entity\User;
use Psr\Http\Message\ServerRequestInterface;

final class LoginPasswordAuthentication implements Authentication
{

    private ?User $user = null;
    public const LOGIN_ATTR = 'login';
    public const PASS_ATTR = 'password';

    public function __construct(
        private readonly UserStorageInterface $userStorage,
    ) {
    }

    public function authenticate(ServerRequestInterface $request): ?User
    {
        $login = $request->getAttribute(self::LOGIN_ATTR, '');
        $password = $request->getAttribute(self::PASS_ATTR, '');

        if ($this->validate($login, $password)) {
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
}
