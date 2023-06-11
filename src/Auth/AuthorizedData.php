<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

final class AuthorizedData
{
    public int $userId;
    public $data = null;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }
}
