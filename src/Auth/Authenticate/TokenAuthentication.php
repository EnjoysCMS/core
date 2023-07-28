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
    protected string $headerName = 'X-Api-Key';
    protected string $pattern = '/(.*)/';

    public function __construct(
        private readonly EntityManager $em,
        private readonly UserStorageInterface $userStorage,
        private readonly Config $config,
    )
    {
    }

    public function withPattern(string $pattern): self
    {
        $new = clone $this;
        $new->pattern = $pattern;
        return $new;
    }

    public function withHeaderName(string $name): self
    {
        $new = clone $this;
        $new->headerName = $name;
        return $new;
    }

    protected function getToken(ServerRequestInterface $request): ?string
    {
        $authHeaders = $request->getHeader($this->headerName);
        $authHeader = reset($authHeaders);
        if (!empty($authHeader)) {
            if (preg_match($this->pattern, $authHeader, $matches)) {
                $authHeader = $matches[1];
            } else {
                return null;
            }
            return $authHeader;
        }
        return null;
    }



    public function authenticate(ServerRequestInterface $request): ?User
    {

        if ($this->checkToken($this->getToken($request))){
            return $this->user;
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

        $this->user = $this->userStorage->getUser($tokenEntity->getUser());
        return true;
    }

}
