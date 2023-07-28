<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\UserRepository;
use Exception;

final class Identity implements IdentityInterface
{

    private ?User $user = null;


    public function __construct(
        private readonly UserStorageInterface $userStorage,
        private Authorize $authorize,
    ) {
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        $this->fetchUserFromAuthorizedData();
        return $this->user ?? $this->userStorage->getGuestUser() ?? throw new Exception('Invalid user');
    }

    private function fetchUserFromAuthorizedData(): void
    {
        $userData = $this->authorize->getAuthorizedData();
        if ($userData === null) {
            return;
        }

        $this->user = $this->userStorage->getUser($userData->userId) ?? $this->userStorage->getGuestUser();
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
