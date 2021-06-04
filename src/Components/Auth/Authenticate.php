<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth;


use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\AccessControl\Autologin;
use EnjoysCMS\Core\Components\AccessControl\Password;
use EnjoysCMS\Core\Components\Detector\Browser;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\Users;

final class Authenticate
{
    private ?Users $user;
    private EntityManager $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function checkLogin(string $login, string $password): bool
    {
        /** @var Users $user */
        $user = $this->em->getRepository(Users::class)->findOneBy(['login' => $login]);
        if ($user === null) {
            return false;
        }
        $this->setUser($user);
        return Password::verify($password, $this->user->getPasswordHash());
    }

    public function checkToken(string $token)
    {
        $now = new \DateTimeImmutable();
        $tokenRepository = $this->em->getRepository(Token::class);
        /** @var Token $tokenEntity */
        $tokenEntity = $tokenRepository->find($token);
        if ($tokenEntity === null) {
            return false;
        }

        if ($tokenEntity->getExp() < $now->getTimestamp()) {
            return false;
        }

        if($tokenEntity->getFingerprint() !== Browser::getFingerprint()){
            return false;
        }

        /** @var Users $user */
        $user = $this->em->getRepository(Users::class)->find($tokenEntity->getUser());
        $this->setUser($user);
        return true;
    }

    /**
     * @return Users|null
     */
    public function getUser(): ?Users
    {
        return $this->user;
    }

    /**
     * @param Users|null $user
     */
    public function setUser(?Users $user): void
    {
        $this->user = $user;
    }


}