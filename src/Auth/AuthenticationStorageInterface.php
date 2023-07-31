<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth;


use EnjoysCMS\Core\Users\Entity\User;

interface AuthenticationStorageInterface
{

    public function setVerified(User $user, array $payload = []): void;
    public function getUserId();
    public function logout(): void;
}
