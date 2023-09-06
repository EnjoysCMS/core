<?php

namespace EnjoysCMS\Core\Auth;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Enjoys\Config\Config;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Exception;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\TokenRepository;
use Ramsey\Uuid\Uuid;

class TokenManage
{

    private string $tokenName;
    private TokenRepository|EntityRepository $repository;

    public function __construct(
        private readonly Config $config,
        private readonly EntityManagerInterface $em,
        private readonly Cookie $cookie
    ) {
        $this->tokenName = $this->config->get('security->token_name') ?? '_token_refresh';
        $this->repository = $this->em->getRepository(Token::class);
    }

    public static function getFingerprint(): string
    {
        return hash_hmac(
            'sha256',
            sprintf(
                "%s%s%s%s",
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $_SERVER['HTTP_ACCEPT'] ?? '',
                $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
                $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''
            ),
            $_ENV['APP_SECRET'] ?? 'secret phrase'
        );
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function write(User $user, string $token = null): void
    {

        $now = new DateTimeImmutable();
        $ttl = $now->modify($this->config->get('security->autologin_cookie_ttl'));


        $tokenEntity = $this->repository->findOneBy(['token' => $token]);

        if ($tokenEntity === null) {
            $tokenEntity = new Token();
            $tokenEntity->setUser($user);
            $tokenEntity->setFingerprint(self::getFingerprint());
        }
        $tokenEntity->setToken(Uuid::uuid4()->toString());
        $tokenEntity->setExp($ttl);
        $tokenEntity->setLastUsed($now);


        $this->cookie->set(
            $this->tokenName,
            $tokenEntity->getToken(),
            $ttl,
            [
                'samesite' => $this->config->get('security->cookie_samesite', 'Lax'),
                'httponly' => $this->config->get('security->cookie_httponly', true),
            ]
        );

        $this->em->persist($tokenEntity);
        $this->repository->clearUsersOldTokens($tokenEntity, $this->config);

        $this->em->flush();
    }

    /**
     * @throws Exception
     */
    public function delete(): void
    {
        $token = $this->cookie->get($this->tokenName);
        $this->cookie->delete($this->tokenName);

        $tokenEntity = $this->repository->find($token ?? '');
        if ($tokenEntity === null) {
            return;
        }
        $this->em->remove($tokenEntity);
        $this->em->flush();
    }
}
