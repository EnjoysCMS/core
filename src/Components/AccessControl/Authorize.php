<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Session\Session;
use Psr\Container\ContainerInterface;

class Authorize
{
    /**
     * @var Authenticate
     */
    private Authenticate $authenticate;
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var Cookie
     */
    private $cookie;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->authenticate = new Authenticate($container->get(Identity::class));
        $this->session = $container->get(Session::class);
        $this->cookie = $container->get(Cookie::class);
        $this->container = $container;
    }

    public function logout(): void
    {
        $this->session->delete('user');
        $this->session->delete('authenticate');
        $this->cookie->delete(Autologin::getTokenName());
    }

    public function byLogin(string $login, string $password, bool $autologin = true): void
    {
        if (!$this->authenticate->checkLogin($login, $password)) {
            $this->logout();
            return;
        }

        $user = $this->authenticate->getUser();

        $this->session->set(
            [
                'user' => [
                    'id' => $user->getId()
                ],
                'authenticate' => 'login'
            ]
        );

        if ($autologin === true) {
            $this->setAutologin();
        }
    }

    public function byAutoLogin()
    {
        if (!$this->authenticate->checkToken(Cookie::get(Autologin::getTokenName()))) {
            $this->logout();
            return;
        }

        $user = $this->authenticate->getUser();

        $this->session->set(
            [
                'user' => [
                    'id' => $user->getId()
                ],
                'authenticate' => 'autologin'
            ]
        );

        return $user;
    }

    public function setAutologin()
    {
        $user = $this->container->get(Identity::class)->getUser();
        if ($user === null) {
            return;
        }
        $ttl = new \DateTime();
        $ttl->modify('+1 day');
        $hash = $ttl->getTimestamp() . '.' . password_hash($user->getId(), \PASSWORD_DEFAULT);

        $this->cookie->set(Autologin::getTokenName(), $hash, $ttl);

        $user->setToken($hash);
        $this->container->get(EntityManager::class)->flush();
    }

}
