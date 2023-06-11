<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Entities\User;
use Exception;

final class Identity
{
    private ObjectRepository|EntityRepository $usersRepository;

    private ?User $user = null;

    public function __construct(EntityManager $em, private Authorize $authorize)
    {
        $this->usersRepository = $em->getRepository(User::class);
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        $this->fetchUserFromAuthorizedData();

        $this->user ??= $this->getUserById(User::GUEST_ID);

        if ($this->user === null) {
            throw new Exception('Invalid user');
        }
        return $this->user;
    }

    public function getUserById(int $id): ?User
    {
        return $this->usersRepository->find($id);
    }

    private function fetchUserFromAuthorizedData(): void
    {
        $userData = $this->authorize->getAuthorizedData();
        if ($userData === null) {
            return;
        }

        $this->user = $this->getUserById($userData->userId);
    }
}
