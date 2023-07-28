<?php

namespace EnjoysCMS\Core\Auth;

use EnjoysCMS\Core\Users\Entity\User;

interface IdentityInterface
{
    public function getUser(): User;
}
