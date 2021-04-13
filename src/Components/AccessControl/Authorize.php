<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Entities\Users;
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
        $this->container = $container;
        $this->authenticate = new Authenticate($container->get(Identity::class));
        $this->session = $container->get(Session::class);
        $this->cookie = $container->get(Cookie::class);

    }

    public function logout(?Users $user = null): void
    {
        if($user !== null){
            $user->setToken(null);
            $this->container->get(EntityManager::class)->flush();
        }


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
            $autologin = new Autologin($this->container);
            $autologin->setHashCookie();
        }
    }

    /**
     * @return Users|void|null
     */
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

}
