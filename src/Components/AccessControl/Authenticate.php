<?php


namespace App\Components\AccessControl;


use App\Entities\Users;

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
