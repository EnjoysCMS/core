<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Components\Auth;

use EnjoysCMS\Core\Components\Auth\Authorize;
use EnjoysCMS\Core\Components\Auth\StrategyInterface;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\User;
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
        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('login');
        $container->method('get')->will(
            $this->onConsecutiveCalls($config, $strategy)
        );
        Config::setContainer($container);

        $authorize = new Authorize($container);

        $authorize->setAuthorized(new User());

    }

    public function testLogout()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('logout');
        $container->method('get')->will(
            $this->onConsecutiveCalls($config, $strategy)
        );
        Config::setContainer($container);

        $authorize = new Authorize($container);

        $authorize->logout();

    }

    public function testGetAuthorizedData()
    {
        $container = $this->getMock(ContainerInterface::class);
        $config = $this->getMock(\Enjoys\Config\Config::class);
        $strategy = $this->getMock(StrategyInterface::class);
        $strategy->expects($this->exactly(1))->method('getAuthorizedData');
        $container->method('get')->will(
            $this->onConsecutiveCalls($config, $strategy)
        );
        Config::setContainer($container);

        $authorize = new Authorize($container);

        $authorize->getAuthorizedData();

    }
}
