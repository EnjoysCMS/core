<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\AuthenticationStorage;


use EnjoysCMS\Core\Auth\AuthenticationStorageInterface;
use EnjoysCMS\Core\Users\Entity\User;

final class Memory implements AuthenticationStorageInterface
{

    private static ?User $user = null;

    public function setVerified(User $user, array $payload = []): void
    {
        self::$user = $user;
    }
    public function getUserId()
    {
        return self::$user?->getId();
    }
}
