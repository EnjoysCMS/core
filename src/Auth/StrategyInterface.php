<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;



use EnjoysCMS\Core\Users\Entity\User;

interface StrategyInterface
{
    public function getAuthorizedData(): ?AuthorizedData;

    public function login(User $user, array $data = []): void;

    public function logout(): void;
}
