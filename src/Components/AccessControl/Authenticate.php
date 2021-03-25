<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use EnjoysCMS\Core\Entities\Users;

class Authenticate
{

    /**
     * @var Identity
     */
    private Identity $identity;
    private ?Users $user;

    public function __construct(Identity $identity)
    {
        $this->identity = $identity;
    }

    public function checkLogin(string $login, string $password): bool
    {
        $user = $this->identity->getUserByLogin($login);
        if ($user === null) {
            return false;
        }
        $this->setUser($user);
        return Password::verify($password, $this->user->getPasswordHash());
    }


    public function checkToken(?string $token): bool
    {

        preg_match ('/^\d+/', $token, $match);
        if($match[0] < time()){
            return false;
        }


        $user = $this->identity->getUserByToken($token);
        if ($user === null) {
            return false;
        }
        $this->setUser($user);
        return true;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    /**
     * @param Users $user
     */
    private function setUser(Users $user): void
    {
        $this->user = $user;
    }



}
