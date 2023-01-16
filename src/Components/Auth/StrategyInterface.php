<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Auth;

use EnjoysCMS\Core\Entities\User;

interface StrategyInterface
{
    public function getAuthorizedData(): ?AuthorizedData;

    public function login(User $user, array $data = []): void;
}
