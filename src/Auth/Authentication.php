<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth;


use EnjoysCMS\Core\Users\Entity\User;
use Psr\Http\Message\ServerRequestInterface;

interface Authentication
{
    public function authenticate(ServerRequestInterface $request): ?User;
}
