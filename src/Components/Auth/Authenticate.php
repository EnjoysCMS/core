<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Auth;

use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\AccessControl\Password;
use EnjoysCMS\Core\Components\Detector\Browser;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\User;

final class Authenticate
{
    private ?User $user;

    public function __construct(private EntityManager $em)
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

    public function checkToken(string $token): bool
    {
        $now = new \DateTimeImmutable();
        $tokenRepository = $this->em->getRepository(Token::class);
        /** @var Token $tokenEntity */
        $tokenEntity = $tokenRepository->find($token);
        if ($tokenEntity === null) {
            return false;
        }

        if ($tokenEntity->getExp() < $now) {
            return false;
        }

        if (Config::get('security', 'check_browser_fingerprint', false)) {
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
