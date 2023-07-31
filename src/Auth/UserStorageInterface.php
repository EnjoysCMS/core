<?php

namespace EnjoysCMS\Core\Auth;

use EnjoysCMS\Core\Users\Entity\User;

interface UserStorageInterface
{

    public function getUser($userId): ?User;

    public function getGuestUser();

    public function getUserByLogin(string $login);
}
