<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth;


final class AuthorizedData
{

    public int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
}