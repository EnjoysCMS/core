<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Users\Repository;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<User> findAll()
 * @method list<User> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends EntityRepository
{

    /**
     * @throws Exception
     */
    public function getGuest(): User
    {
        return $this->find(User::GUEST_ID) ?? throw new \RuntimeException('User::GUEST_ID is incorrect');
    }


}
