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
    private array $config = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get('Config')->getConfig('security');
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
        $ttl->modify($this->config['autologin_cookie_ttl']);

        $hash = $ttl->getTimestamp() . '.' . password_hash($user->getId(), $this->config['algo_password_hash']);

        $this->container->get(Cookie::class)->set(Autologin::getTokenName(), $hash, $ttl);

        $user->setToken($hash);
        $this->container->get(EntityManager::class)->flush();
    }
}
