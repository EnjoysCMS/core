<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

use Enjoys\Config\Config;
use EnjoysCMS\Core\Auth\Strategy\PhpSession;
use EnjoysCMS\Core\Users\Entity\User;
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
    public function __construct(ContainerInterface $container, Config $config)
    {
        $this->strategy = $container->get(
            $config->get('security->auth_strategy', PhpSession::class)
        );
    }

    public function setAuthorized(User $user, array $data = []): bool
    {
        $this->strategy->authorize($user, $data);
        return true;
    }


    public function logout(): void
    {
        $this->strategy->logout();
    }

    public function getAuthorizedData(): ?AuthorizedData
    {
        return $this->strategy->getAuthorizedData();
    }
}
