<?php

namespace EnjoysCMS\Core\Auth;

interface UserStorageInterface
{

    public function getUser($userId);

    public function getGuestUser();

    public function getUserByLogin(string $login);
}
