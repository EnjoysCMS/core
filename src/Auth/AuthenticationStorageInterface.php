<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth;


use EnjoysCMS\Core\Users\Entity\User;

interface AuthenticationStorageInterface
{

    public function setUser(User $user): void;
    public function getUserId();
}
