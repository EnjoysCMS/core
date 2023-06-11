<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth\Strategy;

use Doctrine\ORM\EntityManager;
use Enjoys\Cookie\Cookie;
use Enjoys\Cookie\Options;
use Enjoys\Session\Session;
use EnjoysCMS\Core\Auth\Authenticate;
use EnjoysCMS\Core\Auth\AuthorizedData;
use EnjoysCMS\Core\Auth\Strategy\PhpSession;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\EnjoysCMS\Traits\MockHelper;

class PhpSessionTest extends TestCase
{

    use MockHelper;

    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->request = ServerRequest::fromGlobals();
    }

    protected function tearDown(): void
    {

    }

    public function testLoginWithoutRemember()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $session->expects($this->exactly(1))->method('set');
        $cookie = $this->getMock(Cookie::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


        $authStrategy = new PhpSession($em, $session, $cookie, $config);

        $authStrategy->login(new User(), ['remember' => false]);
    }

    public function testLoginWithRemember()
    {
        $em = $this->getMock(EntityManager::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $session = $this->createMock(Session::class);
        $tokenRepository = $this->createMock(TokenRepository::class);
        $cookie = $this->getMock(Cookie::class);

        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh'],
            ['security->autologin_cookie_ttl', null, '1 month'],
        ]);

        $em->method('getRepository')->willReturn($tokenRepository);

        $cookie->expects($this->exactly(1))->method('set');
        $em->expects($this->exactly(1))->method('persist');
        $em->expects($this->exactly(1))->method('flush');
        $tokenRepository->expects($this->exactly(1))->method('clearUsersOldTokens');

        $authStrategy = new PhpSession($em, $session, $cookie, $config);

        $authStrategy->login(new User(), ['remember' => true]);
    }

    public function testLogout()
    {
        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


        $authStrategy = new PhpSession($em, $session, new Cookie(new Options($this->request)), $config);

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

        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


        $authStrategy = new PhpSession($em, $session, $cookie, $config);

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
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);

        $authStrategy = new PhpSession($em, $session, $cookie, $config);
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
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


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
                'cookie' => new Cookie(new Options($this->request)),
                'config' => $config
            ])->getMock()
        ;
        $authStrategy->method('isAuthorized')->willReturn(true);

        $this->assertInstanceOf(AuthorizedData::class, $authorizedData = $authStrategy->getAuthorizedData());
        $this->assertSame(42, $authorizedData->userId);
    }

    public function testIsAuthorizedTrueBySession()
    {
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $session->method('get')->willReturnMap([
            ['user', null, ['id' => 42]],
            ['authenticate', null, true]
        ]);

        $authStrategy = new PhpSession($em, $session, new Cookie(new Options($this->request)), $config);

        $this->assertTrue($authStrategy->isAuthorized());
    }

    public function testIsAuthorizedTrueByCookie()
    {
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->autologin_cookie_ttl', null, '1 month']
        ]);

        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);

//        $_COOKIE['_token_refresh'] = 'token';


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
                'cookie' => new Cookie(new Options($this->request->withCookieParams([
                    '_token_refresh' => 'token'
                ]))),
                'config' => $config,
            ])
            ->getMock()
        ;

        $result = $authStrategy->isAuthorized(authenticate: $authenticate);
        $this->assertTrue($result);
    }

    public function testIsAuthorizedFalseByCookie()
    {
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->autologin_cookie_ttl', null, '1 month']
        ]);

        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


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
                'cookie' => new Cookie(new Options($this->request->withCookieParams([
                    '_token_refresh' => 'token'
                ]))),
                'config' => $config
            ])
            ->getMock()
        ;

        $authStrategy->expects($this->exactly(1))->method('deleteToken');

        $result = $authStrategy->isAuthorized(authenticate: $authenticate);
        $this->assertFalse($result);
    }


    public function testIsAuthorizedFalse()
    {
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturnMap([
            ['security->token_name', null, '_token_refresh']
        ]);


        $em = $this->getMock(EntityManager::class);
        $session = $this->createMock(Session::class);
        $authStrategy = new PhpSession($em, $session, new Cookie(new Options($this->request)), $config);

        $this->assertFalse($authStrategy->isAuthorized());
    }
}
