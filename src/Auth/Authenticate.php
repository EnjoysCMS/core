<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Enjoys\Config\Config;
use EnjoysCMS\Core\AccessControl\Password;
use EnjoysCMS\Core\Detector\Browser;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Authenticate
{
    private ?User $user;

    public function __construct(private EntityManager $em, private Config $config)
    {
    }

    public function checkLogin(string $login, string $password): bool
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $login]);
        if ($user === null) {
            return false;
        }
        $this->setUser($user);
        return Password::verify($password, $this->user->getPasswordHash());
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function checkToken(string $token): bool
    {
        $now = new DateTimeImmutable();
        $tokenRepository = $this->em->getRepository(Token::class);
        /** @var Token $tokenEntity */
        $tokenEntity = $tokenRepository->find($token);
        if ($tokenEntity === null) {
            return false;
        }

        if ($tokenEntity->getExp() < $now) {
            return false;
        }

        if ($this->config->get('security->check_browser_fingerprint', false)) {
            if ($tokenEntity->getFingerprint() !== Browser::getFingerprint()) {
                return false;
            }
        }

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($tokenEntity->getUser());
        $this->setUser($user);
        return true;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
