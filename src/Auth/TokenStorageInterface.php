<?php

namespace EnjoysCMS\Core\Auth;

interface TokenStorageInterface
{

    public function find(string $token);
}
