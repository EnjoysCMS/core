<?php

namespace EnjoysCMS\Core\Auth\UserStorage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\UserRepository;

class DatabaseUserStorage implements UserStorageInterface
{

    public function __construct(private readonly UserRepository $repository)
    {
    }

    public function getUser($userId): ?User
    {
        return $this->repository->find($userId ?? 0);
    }

    public function getUserByLogin($login)
    {
        return $this->repository->findOneBy(['login' => $login]);
    }

    /**
     * @throws \Exception
     */
    public function getGuestUser(): User
    {
        return $this->repository->getGuest();
    }


}
