<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Enjoys\Cookie\Cookie;
use EnjoysCMS\Core\Entities\Users;
use Doctrine\ORM\EntityManager;
use Enjoys\Session\Session;
use Psr\Container\ContainerInterface;

class Identity
{

    /**
     * @var Users
     */
    private $usersRepository;
    /**
     * @var Session
     */
    private Session $session;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->usersRepository = $this->container->get(EntityManager::class)->getRepository(Users::class);
        $this->session = $this->container->get(Session::class);

    }

    public function getUserByLogin(string $login): ?Users
    {
        return $this->usersRepository->findOneBy(['login' => $login]);
    }

    public function getUserByToken(string $token): ?Users
    {

        return $this->usersRepository->findOneBy(['token' => $token]);
    }

    public function getUserById(int $id): ?Users
    {
        return $this->usersRepository->find($id);
    }

    public function getUser()
    {
        $user = null;

        if (isset($this->session->get('user')['id']) && $this->session->get('authenticate') !== null) {
            $user = $this->getUserById($this->session->get('user')['id']);
        }

        if($user === null && Cookie::has(Autologin::getTokenName())){
            $user = $this->container->get(Authorize::class)->byAutoLogin();
        }

        return $user ?? $this->getUserById(Users::GUEST_ID);
    }

}
