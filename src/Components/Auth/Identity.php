<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Entities\Users;

final class Identity
{
    private EntityManager $em;


    /**
     * @var \Doctrine\ORM\EntityRepository|\Doctrine\Persistence\ObjectRepository
     */
    private $usersRepository;
    private Authorize $authorize;

    private ?Users $user = null;

    public function __construct(EntityManager $em, Authorize $authorize)
    {
        $this->em = $em;
        $this->usersRepository = $em->getRepository(Users::class);
        $this->authorize = $authorize;
    }

    /**
     * @throws \Exception
     */
    public function getUser(): Users
    {
        $this->fetchUserFromAuthorizedData();

        $this->user ??= $this->getUserById(Users::GUEST_ID);

        if ($this->user === null) {
            throw new \Exception('Invalid user');
        }
        return $this->user;
    }

    public function getUserById(int $id): ?Users
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