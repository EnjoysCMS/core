<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\Authenticate;


use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Users\Entity\User;

final class TokenAuthentication implements Authentication
{

    public function getUser(): ?User
    {
        return null;
    }
}
