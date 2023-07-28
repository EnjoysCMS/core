<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth\Strategy;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Enjoys\Config\Config;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Exception;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Auth\Authenticate;
use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Auth\AuthorizedData;
use EnjoysCMS\Core\Auth\StrategyInterface;
use EnjoysCMS\Core\Detector\Browser;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Entity\User;
use EnjoysCMS\Core\Users\Repository\TokenRepository;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;

final class PhpSession implements StrategyInterface
{

    private string $tokenName;

    public function __construct(
        private readonly EntityManager $em,
        private readonly Session $session,
        private readonly Cookie $cookie,
        private readonly Config $config,
        private readonly Authenticate\TokenAuthentication $tokenAuthentication,
        private readonly ServerRequestInterface $request,
    ) {
        $this->tokenName = $this->config->get('security->token_name') ?? '_token_refresh';
    }


    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     */
    public function authorize(?User $user, array $data = []): void
    {
        if ($user === null) {
            $this->logout();
            return;
        }

        $this->session->set(
            [
                'user' => [
                    'id' => $user->getId()
                ],
                'authenticate' => $data['authenticate'] ?? 'login'
            ]
        );

        if ($data['remember'] ?? true) {
            $this->writeToken($user, $data['token'] ?? null);
        }
    }


    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     */
    public function logout(): void
    {
        $this->session->delete('user');
        $this->session->delete('authenticate');
        $this->deleteToken();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getAuthorizedData(): ?AuthorizedData
    {
        if ($this->isAuthorized()) {
            $authorizedData = new AuthorizedData((int)$this->session->get('user')['id']);
            $authorizedData->data = $this->session->get('user');
            return $authorizedData;
        }
        return null;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function isAuthorized($retry = 0, ?Authentication $authentication = null): bool
    {
        if (isset($this->session->get('user')['id']) && $this->session->get('authenticate') !== null) {
            return true;
        }

        if ($this->cookie->has($this->tokenName) && $retry < 1) {
            $retry++;
            $authentication = $authentication ?? $this->tokenAuthentication->withHeaderName('X-REFRESH-TOKEN');
            $token = $this->cookie->get($this->tokenName);
            $this->authorize(
                $authentication->authenticate($this->request->withAddedHeader('X-REFRESH-TOKEN', $token)),
                ['authenticate' => 'autologin']
            );
            return $this->isAuthorized($retry, $authentication);
        }
        //    $this->deleteToken();
        return false;
    }

    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     * @throws \Exception
     */
    public function writeToken(User $user, string $token = null)
    {
        $now = new DateTimeImmutable();
        $ttl = $now->modify($this->config->get('security->autologin_cookie_ttl'));

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
            $this->tokenName,
            $tokenEntity->getToken(),
            $ttl,
            [
                'samesite' => $this->config->get('security->cookie_samesite', 'Lax'),
                'httponly' => $this->config->get('security->cookie_httponly', true),
            ]
        );

        $this->em->persist($tokenEntity);
        $this->em->flush();

        $tokenRepository->clearUsersOldTokens($tokenEntity, $this->config);
    }


    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     */
    public function deleteToken(): void
    {
        $token = $this->cookie->get($this->tokenName);

        $this->cookie->delete($this->tokenName);
        $tokenRepository = $this->em->getRepository(Token::class);
        $tokenEntity = $tokenRepository->find($token);
        if ($tokenEntity === null) {
            return;
        }
        $this->em->remove($tokenEntity);
        $this->em->flush();
    }
}
