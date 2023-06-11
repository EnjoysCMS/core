<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth;

use EnjoysCMS\Core\Auth\Authorize;
use EnjoysCMS\Core\Auth\StrategyInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\EnjoysCMS\Traits\MockHelper;

class AuthorizeTest extends TestCase
{
    use MockHelper;

    public function testSetAuthorized()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturn('strategy-class-string');

        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('login');
        $container->method('get')->willReturn($strategy);

        $authorize = new Authorize($container, $config);

        $authorize->setAuthorized(new User());

    }

    public function testLogout()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturn('strategy-class-string');
        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('logout');
        $container->method('get')->willReturn($strategy);

        $authorize = new Authorize($container, $config);

        $authorize->logout();

    }

    public function testGetAuthorizedData()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $config->method('get')->willReturn('strategy-class-string');

        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('getAuthorizedData');
        $container->method('get')->willReturn($strategy);

        $authorize = new Authorize($container, $config);

        $authorize->getAuthorizedData();

    }
}
