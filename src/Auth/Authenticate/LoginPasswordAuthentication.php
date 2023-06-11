<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\Authenticate;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Users\Entity\User;

final class LoginPasswordAuthentication implements Authentication
{

    private ?User $user = null;

    public function __construct(
        private readonly EntityManager $em
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function validate(string $login, string $password): bool
    {
        /** @var null|User $user */
        $user = $this->em->getRepository(User::class)->findOneBy([
            'login' => $login
        ]);

        if ($user === null) {
            return false;
        }

        $isVerify = password_verify($password, $user->getPasswordHash());

        if ($isVerify === true) {
            $this->user = $user;
        }

        return $isVerify;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
