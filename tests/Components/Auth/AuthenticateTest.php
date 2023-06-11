<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\AccessControl\Password;
use EnjoysCMS\Core\Auth\Authenticate;
use EnjoysCMS\Core\Helpers\Config;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\User;
use EnjoysCMS\Core\Repositories\TokenRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
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
        $authenticate = new Authenticate(
            $em,
            $this->getMock(\Enjoys\Config\Config::class)
        );

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
        $authenticate = new Authenticate(
            $em,
            $this->getMock(\Enjoys\Config\Config::class)
        );
        $this->assertFalse($authenticate->checkLogin($login, $password));
    }

    public function testCheckTokenTrue()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $token = 'token';
        $tokenRepository = $this->getMock(TokenRepository::class);
        $userRepository = $this->getMock(EntityRepository::class);
        $em = $this->getMock(EntityManager::class);


        $tokenEntity = new Token();
        $tokenEntity->setExp(new \DateTimeImmutable('+1 day'));
        $tokenEntity->setUser(new User());

        $tokenRepository->method('find')->willReturn($tokenEntity);
        $userRepository->method('find')->willReturn($tokenEntity->getUser());

        $em->method('getRepository')->willReturnMap([
            [User::class, $userRepository],
            [Token::class, $tokenRepository],
        ]);

        $authenticate = new Authenticate(
            $em,
            $this->getMock(\Enjoys\Config\Config::class)
        );
        $this->assertTrue($authenticate->checkToken($token));
        $this->assertInstanceOf(User::class, $authenticate->getUser());
    }

    public function testCheckTokenFalseIfTokenNotExist()
    {
        $token = 'token';
        $tokenRepository = $this->getMock(TokenRepository::class);
        $em = $this->getMock(EntityManager::class);

        $tokenRepository->method('find')->willReturn(null);
        $em->method('getRepository')->willReturn($tokenRepository);

        $authenticate = new Authenticate(
            $em,
            $this->getMock(\Enjoys\Config\Config::class)
        );
        $this->assertFalse($authenticate->checkToken($token));
    }

    public function testCheckTokenFalseIfTokenExpiration()
    {
        $token = 'token';
        $tokenRepository = $this->getMock(TokenRepository::class);
        $em = $this->getMock(EntityManager::class);

        $tokenEntity = new Token();
        $tokenEntity->setExp(new \DateTimeImmutable('-1 day'));

        $tokenRepository->method('find')->willReturn($tokenEntity);
        $em->method('getRepository')->willReturn($tokenRepository);
        $authenticate = new Authenticate(
            $em,
            $this->getMock(\Enjoys\Config\Config::class)
        );
        $this->assertFalse($authenticate->checkToken($token));
    }
}
