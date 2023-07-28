<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Auth\Authenticate;


use Doctrine\ORM\EntityManager;
use Enjoys\Config\Config;
use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Auth\IdentityInterface;
use EnjoysCMS\Core\Auth\UserStorageInterface;
use EnjoysCMS\Core\Detector\Browser;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Entity\User;
use Psr\Http\Message\ServerRequestInterface;

final class TokenAuthentication implements Authentication
{

    private ?User $user = null;
    private string $tokenName;

    public function __construct(
        private readonly EntityManager $em,
        private UserStorageInterface $userStorage,
        private readonly Config $config,
    )
    {
        $this->tokenName = $this->config->get('security->token_name') ?? '_token_refresh';
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function authenticate(ServerRequestInterface $request): ?User
    {

        if ($this->checkToken($request->getCookieParams()[$this->tokenName] ?? null)){
            return $this->getUser();
        }
        return null;
    }

    private function checkToken(?string $token): bool
    {
        if ($token === null){
            return false;
        }
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

        if ($this->config->get('security->check_browser_fingerprint', false)) {
            if ($tokenEntity->getFingerprint() !== Browser::getFingerprint()) {
                return false;
            }
        }

        $this->setUser($this->userStorage->getUser($tokenEntity->getUser()));
        return true;
    }

    private function setUser(User $user)
    {
        $this->user = $user;
    }
}
