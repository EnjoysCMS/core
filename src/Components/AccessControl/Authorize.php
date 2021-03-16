<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Enjoys\Session\Session;

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

    public function __construct(Identity $identity, Session $session)
    {
        $this->authenticate = new Authenticate($identity);

        $this->session = $session;
    }

    public function byLogin(string $login, string $password): void
    {
        if (!$this->authenticate->checkLogin($login, $password)) {
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
    }

    public function byToken()
    {
    }
}
