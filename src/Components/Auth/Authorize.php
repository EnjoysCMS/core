<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Auth;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Enjoys\Cookie\Exception;
use EnjoysCMS\Core\Components\Auth\Strategy\PhpSession;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\User;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Authorize
{
    private StrategyInterface $strategy;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $strategy = Config::get('security', 'auth_strategy', PhpSession::class);
        $this->strategy = $container->get($strategy);
    }

    public function setAuthorized(User $user, array $data = []): void
    {
        $this->strategy->login($user, $data);
    }


    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     */
    public function logout(): void
    {
        $this->strategy->logout();
    }

    public function getAuthorizedData(): ?AuthorizedData
    {
        return $this->strategy->getAuthorizedData();
    }
}
