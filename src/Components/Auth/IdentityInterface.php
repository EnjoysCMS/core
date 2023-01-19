<?php

namespace EnjoysCMS\Core\Components\Auth;

use EnjoysCMS\Core\Entities\User;
use Exception;

interface IdentityInterface
{
    /**
     * @throws Exception
     */
    public function getUser(): User;


    public function getUserById(array|int|string $id): ?User;
}
