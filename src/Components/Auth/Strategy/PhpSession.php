<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth\Strategy;


use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Components\Auth\Authenticate;
use EnjoysCMS\Core\Components\Auth\AuthorizedData;
use EnjoysCMS\Core\Components\Auth\StrategyInterface;
use EnjoysCMS\Core\Components\Detector\Browser;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\Users;
use EnjoysCMS\Core\Repositories\TokenRepository;
use Ramsey\Uuid\Uuid;

final class PhpSession implements StrategyInterface
{
    private Session $session;
    private EntityManager $em;
    private Cookie $cookie;
    private ?array $config;

    public function __construct(EntityManager $em, Session $session, Cookie $cookie)
    {
        $this->session = $session;
        $this->em = $em;
        $this->cookie = $cookie;
        $this->config = Config::getAll('security');
    }


    public function login(Users $user, array $data = []): void
    {
        $this->session->set(
            [
                'user' => [
                    'id' => $user->getId()
                ],
                'authenticate' => $data['authenticate'] ?? 'login'
            ]
        );

        $this->writeToken($user, $data['token'] ?? null);
    }

    public function logout(): void
    {
        $this->session->delete('user');
        $this->session->delete('authenticate');
        if ($this->cookie::has(Token::getTokenName())) {
            $token = $this->cookie::get(Token::getTokenName());
            $this->deleteToken($token);
        }
    }

    public function getAuthorizedData(): ?AuthorizedData
    {
        if ($this->isAuthorized()) {
            $authorizedData = new AuthorizedData((int)$this->session->get('user')['id']);
            $authorizedData->data = $this->session->get('user');
            return $authorizedData;
        }
        return null;
    }

    public function isAuthorized($retry = 0): bool
    {
        if (isset($this->session->get('user')['id']) && $this->session->get('authenticate') !== null) {
            return true;
        }

        if ($this->cookie::has(Token::getTokenName()) && $retry < 1) {
            $retry++;
            $authenticate = new Authenticate($this->em);
            $token = $this->cookie::get(Token::getTokenName());
            if ($authenticate->checkToken($token)) {
                $this->login($authenticate->getUser(), ['authenticate' => 'autologin', 'token' => $token]);
                return $this->isAuthorized($retry);
            }
            $this->deleteToken($token);
        }

        return false;
    }

    public function writeToken(Users $user, string $token = null)
    {
        $now = new \DateTimeImmutable();
        $ttl = $now->modify($this->config['autologin_cookie_ttl']);

        /** @var TokenRepository $tokenRepository */
        $tokenRepository = $this->em->getRepository(Token::class);
        $tokenEntity = $tokenRepository->findOneBy(['token' => $token]);

        if ($tokenEntity === null) {
            $tokenEntity = new Token();
            $tokenEntity->setUser($user);
            $tokenEntity->setFingerprint(Browser::getFingerprint());
        }
        $tokenEntity->setToken(Uuid::uuid4()->toString());
        $tokenEntity->setExp($ttl);
        $tokenEntity->setLastUsed($now);


        $this->cookie->set(
            Token::getTokenName(),
            $tokenEntity->getToken(),
            $ttl,
            [
                'samesite' => $this->config['cookie_samesite'] ?? 'Strict',
                'httponly' => $this->config['cookie_httponly'] ?? true,
            ]
        );

        $this->em->persist($tokenEntity);
        $this->em->flush();

        $tokenRepository->clearUsersOldTokens($tokenEntity);

    }

    public function deleteToken(string $token): void
    {
        $this->cookie->delete(Token::getTokenName());

        $tokenRepository = $this->em->getRepository(Token::class);
        $tokenEntity = $tokenRepository->find($token);
        if ($tokenEntity === null) {
            return;
        }
        $this->em->remove($tokenEntity);
        $this->em->flush();
    }
}