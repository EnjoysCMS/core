<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth\Strategy;

use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Components\Auth\Authenticate;
use EnjoysCMS\Core\Components\Auth\AuthorizedData;
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

    protected function tearDown(): void
    {
        $_COOKIE = [];
    }

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
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);


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

    public function testGetAuthorizedDataIfNotAuthorize()
    {
        $authStrategy = $this->getMockBuilder(PhpSession::class)->onlyMethods([
            'isAuthorized'
        ])->disableOriginalConstructor()->getMock();
        $authStrategy->method('isAuthorized')->willReturn(false);

        $this->assertNull($authStrategy->getAuthorizedData());
    }

    public function testGetAuthorizedDataIfAuthorize()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);

        $session->method('get')->willReturn([
            'id' => 42
        ]);

        $authStrategy = $this->getMockBuilder(PhpSession::class)->onlyMethods([
            'isAuthorized'
        ])
            ->setConstructorArgs([
                'session' => $session,
                'em' => $em,
                'cookie' => new Cookie(new Options())
            ])->getMock()
        ;
        $authStrategy->method('isAuthorized')->willReturn(true);

        $this->assertInstanceOf(AuthorizedData::class, $authorizedData = $authStrategy->getAuthorizedData());
        $this->assertSame(42, $authorizedData->userId);
    }

    public function testIsAuthorizedTrueBySession()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $session->method('get')->willReturnMap([
            ['user', null, ['id' => 42]],
            ['authenticate', null, true]
        ]);

        $authStrategy = new PhpSession($em, $session, new Cookie(new Options()));

        $this->assertTrue($authStrategy->isAuthorized());
    }

    public function testIsAuthorizedTrueByCookie()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('getConfig')->willReturn([
            'autologin_cookie_ttl' => '1 month'
        ]);

        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $_COOKIE[Token::getTokenName()] = 'token';


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);

        $session->method('get')->will(
            $this->onConsecutiveCalls(null, ['id' => 42], true)
        );

        $session->expects($this->exactly(3))->method('get');

        $authenticate = $this->createMock(Authenticate::class);

        $authenticate->method('getUser')->willReturn(new User());
        $authenticate->method('checkToken')->willReturn(true);


        $authStrategy = $this->getMockBuilder(PhpSession::class)->onlyMethods([
            'login',
            'deleteToken',
            'getAuthorizedData'
        ])
            ->setConstructorArgs([
                'session' => $session,
                'em' => $em,
                'cookie' => new Cookie(new Options())
            ])
            ->getMock()
        ;

        $result = $authStrategy->isAuthorized(authenticate: $authenticate);
        $this->assertTrue($result);
    }

    public function testIsAuthorizedFalseByCookie()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('getConfig')->willReturn([
            'autologin_cookie_ttl' => '1 month'
        ]);

        $container->method('get')->willReturn($config);
        Config::setContainer($container);

        $_COOKIE[Token::getTokenName()] = 'token';


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);

        $session->expects($this->exactly(1))->method('get');

        $authenticate = $this->createMock(Authenticate::class);

        $authenticate->method('getUser')->willReturn(new User());
        $authenticate->method('checkToken')->willReturn(false);


        $authStrategy = $this->getMockBuilder(PhpSession::class)->onlyMethods([
            'login',
            'deleteToken',
            'getAuthorizedData'
        ])
            ->setConstructorArgs([
                'session' => $session,
                'em' => $em,
                'cookie' => new Cookie(new Options())
            ])
            ->getMock()
        ;

        $authStrategy->expects($this->exactly(1))->method('deleteToken');

        $result = $authStrategy->isAuthorized(authenticate: $authenticate);
        $this->assertFalse($result);
    }


    public function testIsAuthorizedFalse()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $container->method('get')->willReturn($config);
        Config::setContainer($container);


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $authStrategy = new PhpSession($em, $session, new Cookie(new Options()));

        $this->assertFalse($authStrategy->isAuthorized());
    }
}
