<?php

namespace Tests\EnjoysCMS\Components\AccessControl;

use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\AccessControl\ACL;
use EnjoysCMS\Core\Auth\Identity;
use EnjoysCMS\Core\Extensions\Twig\AclTwigExtension;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Tests\EnjoysCMS\TestHelper;
use Tests\EnjoysCMS\Traits\MockHelper;
use Twig\Test\IntegrationTestCase;

class ACLTwigExtensionTest extends IntegrationTestCase
{

    use MockHelper;


    protected function getFixturesDir(): string
    {
        return __DIR__ . '/../../_fixtures';
    }

    /**
     * @throws Exception
     */
    protected function getExtensions(): array
    {
        $routes = [
            '@access' => 'access',
            '@notAccess' => 'not access',
            'test' => 'access',
            'test2' => 'not access',
        ];

        $acl = $this->getAclImpl($routes, [
            1,
            3
        ]);

        $routeCollection = $this->getMockBuilder(RouteCollection::class)->disableOriginalConstructor()->getMock();


        $routeCollection->method('get')->willReturnCallback(function ($route) use ($routes) {
            return TestHelper::getRoutes($this->getMock(Route::class), $routes)[$route] ?? null;
        });

        return [
            new AclTwigExtension($acl, $routeCollection)
        ];
    }

    /**
     * @throws Exception
     */
    private function getAclImpl(array $aclData = [], array $aclAccessIds = []): ACL
    {
        $aclRepository = $this->getMock(\EnjoysCMS\Core\Repositories\ACL::class);
        $aclRepository->method('findAll')->willReturn(
            TestHelper::getAclEntities($aclEntity = $this->getMock(\EnjoysCMS\Core\Entities\ACL::class), $aclData)
        );
        $aclRepository->method('findAcl')->willReturn($aclEntity);

        $em = $this->getMock(EntityManager::class);
        $em->method('getRepository')->willReturn($aclRepository);

        $userEntity = $this->getMock(User::class);

        $userEntity->method('getAclAccessIds')->willReturn($aclAccessIds);

        $identity = $this->getMock(Identity::class);
        $identity->method('getUser')->willReturn($userEntity);

        return new ACL($em, $identity);
    }
}
