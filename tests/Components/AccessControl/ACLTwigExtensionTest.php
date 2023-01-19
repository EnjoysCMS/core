<?php

namespace Tests\EnjoysCMS\Components\AccessControl;

use EnjoysCMS\Core\Components\AccessControl\ACLTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tests\EnjoysCMS\MocksTrait;
use Twig\Test\IntegrationTestCase;

class ACLTwigExtensionTest extends IntegrationTestCase
{

    use MocksTrait;

    protected function getFixturesDir()
    {
        return __DIR__ . '/../../_fixtures';
    }

    protected function getExtensions()
    {
        $routes = [
            '@access' => 'access',
            '@notAccess' => 'not access',
            'test' => 'access',
            'test2' => 'not access',
        ];
        $userEntity = $this->getUserEntityMock();

        $userEntity->method('getAclAccessIds')->willReturn([
            1, 3
        ]);

        $acl = $this->getACL($userEntity, $this->getAcls($routes));

        $routeCollection = $this->getMockBuilder(RouteCollection::class)->disableOriginalConstructor()->getMock();


        $routeCollection->method('get')->willReturnCallback(function ($route) use ($routes) {
            return $this->getRoutes($routes)[$route] ?? null;
        });

        return [
            new ACLTwigExtension($acl, $routeCollection)
        ];
    }

    private function getRoutes(array $inputValues): array
    {
        $result = [];
        foreach ($inputValues as $action => $comment) {
            $route = $this->getMockBuilder(Route::class)->disableOriginalConstructor()->getMock();
            $route->method('getDefault')->willReturn($action);
            $route->method('getOption')->willReturn($comment);
            $result[$action] = $route;
        }

        return $result;
    }

    private function getAcls(array $inputValues): array
    {
        $result = [];
        $i = 1;
        foreach ($inputValues as $action => $comment) {
            $aclEntity = $this->getAclEntityMock();
            $aclEntity->method('getId')->willReturn($i);
            $aclEntity->method('getAction')->willReturn($action);
            $aclEntity->method('getComment')->willReturn($comment);
            $result[$action] = $aclEntity;
            $i++;
        }

        return $result;
    }
}
