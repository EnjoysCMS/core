<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth\Strategy;

use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Components\Auth\Strategy\PhpSession;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\User;
use EnjoysCMS\Core\Repositories\TokenRepository;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\EnjoysCMS\Traits\MockHelper;

class PhpSessionTest extends TestCase
{

    use MockHelper;

    public function testLoginWithoutRemember()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $session->expects($this->exactly(1))->method('set');
        $container = $this->getMock(ContainerInterface::class);
        $cookie = $this->getMock(Cookie::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $authStrategy = new PhpSession($em, $session, $cookie);

        $authStrategy->login(new User(), ['remember' => false]);
    }

    public function testLoginWithRemember()
    {
        $em = $this->getMock(EntityManager::class);
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $session = $this->createMock(Session::class);
        $tokenRepository = $this->createMock(TokenRepository::class);
        $cookie = $this->getMock(Cookie::class);

        $em->method('getRepository')->willReturn($tokenRepository);



        $config->method('getConfig')->willReturn([
            'autologin_cookie_ttl' => '1 month'
        ]);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $cookie->expects($this->exactly(1))->method('set');
        $em->expects($this->exactly(1))->method('persist');
        $em->expects($this->exactly(1))->method('flush');
        $tokenRepository->expects($this->exactly(1))->method('clearUsersOldTokens');

        $authStrategy = new PhpSession($em, $session, $cookie);

        $authStrategy->login(new User(), ['remember' => true]);
    }

    public function testLogout()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $authStrategy = new PhpSession($em, $session, new Cookie(new Options()));
        $session->expects($this->exactly(2))->method('delete');

        $authStrategy->logout();
    }

    public function testDeleteTokenIfExist()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $cookie = $this->getMock(Cookie::class);
        $tokenRepository = $this->createMock(TokenRepository::class);
        $tokenRepository->method('find')->willReturn(new Token());
        $em->method('getRepository')->willReturn($tokenRepository);
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $authStrategy = new PhpSession($em, $session, $cookie);
        $em->expects($this->exactly(1))->method('remove');
        $em->expects($this->exactly(1))->method('flush');
        $authStrategy->deleteToken('token');
    }

    public function testDeleteTokenIfNotExist()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $cookie = $this->getMock(Cookie::class);
        $tokenRepository = $this->createMock(TokenRepository::class);
        $tokenRepository->method('find')->willReturn(null);
        $em->method('getRepository')->willReturn($tokenRepository);
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $authStrategy = new PhpSession($em, $session, $cookie);
        $em->expects($this->exactly(0))->method('remove');
        $em->expects($this->exactly(0))->method('flush');
        $authStrategy->deleteToken('token');
    }
}
