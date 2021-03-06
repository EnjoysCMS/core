<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Auth;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Enjoys\Cookie\Exception;
use EnjoysCMS\Core\Components\Auth\Strategy\PhpSession;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\User;
use Psr\Container\ContainerInterface;

final class Authorize
{
    private StrategyInterface $strategy;

    public function __construct(ContainerInterface $container)
    {
        $strategy = Config::get('security', 'auth_strategy', PhpSession::class);
        $this->strategy = $container->get($strategy);
    }

    public function setAuthorized(User $user)
    {
        $this->strategy->login($user);
    }

    /**
     * @throws OptimisticLockException
     * @throws Exception
     * @throws ORMException
     */
    public function logout()
    {
        $this->strategy->logout();
    }

    public function getAuthorizedData(): ?AuthorizedData
    {
        return $this->strategy->getAuthorizedData();
    }
}