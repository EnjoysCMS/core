<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\AccessControl\Password;
use EnjoysCMS\Core\Components\Auth\Authenticate;
use EnjoysCMS\Core\Entities\User;
use PHPUnit\Framework\TestCase;
use Tests\EnjoysCMS\Traits\MockHelper;

class AuthenticateTest extends TestCase
{
    use MockHelper;

    public function testCheckLoginTrue()
    {
        $login = 'login';
        $password = 'password';
        $passwordHash = Password::getHash($password);
        $em = $this->getMock(EntityManager::class);
        $userRepository = $this->getMock(EntityRepository::class);

        $user = new User();
        $user->setPasswordHash($passwordHash);

        $userRepository->method('findOneBy')->willReturn($user);
        $em->method('getRepository')->willReturn($userRepository);
        $authenticate = new Authenticate($em);

        $this->assertTrue($authenticate->checkLogin($login, $password));

    }

    public function testCheckLoginFalse()
    {
        $login = 'login';
        $password = 'password';
        $em = $this->getMock(EntityManager::class);
        $userRepository = $this->getMock(EntityRepository::class);

        $userRepository->method('findOneBy')->willReturn(null);
        $em->method('getRepository')->willReturn($userRepository);
        $authenticate = new Authenticate($em);
        $this->assertFalse($authenticate->checkLogin($login, $password));

    }
}
