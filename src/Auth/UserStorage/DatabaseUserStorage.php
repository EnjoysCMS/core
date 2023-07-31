<?php

namespace EnjoysCMS\Core\Auth\UserStorage;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\UserRepository;

class DatabaseUserStorage implements UserStorageInterface
{
    private UserRepository|EntityRepository $repository;

    /**
     * @throws NotSupported
     */
    public function __construct(private readonly EntityManager $em)
    {
        $this->repository = $this->em->getRepository(User::class);

    }

    public function getUser($userId): ?User
    {
        return $this->repository->find($userId ?? 0);
    }

    public function getUserByLogin($login)
    {
        return $this->repository->findOneBy(['login' => $login]);
    }

    public function getGuestUser()
    {
        return $this->repository->getGuest();
    }


}
