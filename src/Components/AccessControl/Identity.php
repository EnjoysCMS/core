<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use EnjoysCMS\Core\Entities\Users;
use Doctrine\ORM\EntityManager;
use Enjoys\Session\Session;

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

    public function __construct(EntityManager $entityManager, Session $session)
    {
        $this->usersRepository = $entityManager->getRepository(Users::class);
        $this->session = $session;
    }

    public function getUserByLogin(string $login): ?Users
    {
        return $this->usersRepository->findOneBy(['login' => $login]);
    }

    public function getUserById(int $id): ?Users
    {
        return $this->usersRepository->find($id);
    }

    public function getUser()
    {
        $id = Users::GUEST_ID;

        $user = $this->session->get('user');
        if (isset($user['id']) && $this->session->get('authenticate') !== null) {
            $id = $user['id'];
        }

        return $this->getUserById($id);
    }

}
