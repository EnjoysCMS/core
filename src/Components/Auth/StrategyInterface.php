<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth;


use EnjoysCMS\Core\Entities\Users;

interface StrategyInterface
{
    public function getAuthorizedData(): ?AuthorizedData;
    public function login(Users $user, array $data = []): void;
}