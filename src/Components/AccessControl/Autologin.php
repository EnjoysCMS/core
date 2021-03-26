<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Psr\Container\ContainerInterface;

class Autologin
{

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getTokenName()
    {
        return sprintf('app%u', crc32('autologin'));
    }

    public function setHashCookie()
    {
        $user = $this->container->get(Identity::class)->getUser();
        if ($user === null) {
            return;
        }
        $ttl = new \DateTime();
        $ttl->modify('+1 day');
        $hash = $ttl->getTimestamp() . '.' . password_hash($user->getId(), \PASSWORD_DEFAULT);

        $this->container->get(Cookie::class)->set(Autologin::getTokenName(), $hash, $ttl);

        $user->setToken($hash);
        $this->container->get(EntityManager::class)->flush();
    }
}