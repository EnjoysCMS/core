<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth;


use EnjoysCMS\Core\Users\Entity\User;

interface Authentication
{
    public function getUser(): ?User;
}
