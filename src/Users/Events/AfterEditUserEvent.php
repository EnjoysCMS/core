<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Users\Events;

use EnjoysCMS\Core\Users\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

final class AfterEditUserEvent extends Event
{

    public function __construct(private readonly User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
