<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Users\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Enjoys\Config\Config;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;

use function random_int;

class UserRepository extends EntityRepository
{

    /**
     * @throws Exception
     */
    public function getGuest(): User
    {
        return $this->find(User::GUEST_ID);
    }


}
