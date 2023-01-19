<?php

namespace Tests\EnjoysCMS\Components\AccessControl;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\AccessControl\ACL;
use EnjoysCMS\Core\Components\Auth\Authorize;
use EnjoysCMS\Core\Components\Auth\AuthorizedData;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Components\Auth\StrategyInterface;
use EnjoysCMS\Core\Entities\User;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\EnjoysCMS\MocksTrait;

class ACLTest extends TestCase
{

    use MocksTrait;

    private function getAuthorize(int $userId = 1): Authorize
    {
        $container = $this->getContainerInterfaceMock();

        $strategyAuthorize = $this->getMockBuilder(StrategyInterface::class)->getMock();
        $strategyAuthorize->method(
            'getAuthorizedData'
        )->willReturn(new AuthorizedData($userId));

        $container->method('get')->willReturn($strategyAuthorize);
        return new Authorize($container);
    }


    private function getIdentity(User $userEntity, int $userId = 1): Identity
    {
        $em = $this->getEntityManagerMock();


        $usersRepository = $this->getEntityRepositoryMock();
        $usersRepository->method('find')->willReturn($userEntity);


        $em->method('getRepository')->willReturn($usersRepository);

        return new Identity($em, $this->getAuthorize($userId));
    }

    public function testAccess()
    {
        $userEntity = $this->getUserEntityMock();

        $userEntity->method('getAclAccessIds')->willReturn([
            1
        ]);

        $aclEntity = $this->getAclEntityMock();
        $aclEntity->method('getId')->willReturn(1);
        $aclEntity->method('getAction')->willReturn('test');

        $aclRepository = $this->getAclRepositoryMock();
        $aclRepository->method('findAll')->willReturn([$aclEntity]);
        $aclRepository->method('findAcl')->willReturn($this->getAclEntityMock());

        $em = $this->getEntityManagerMock();
        $em->method('getRepository')->willReturn($aclRepository);

        $idendity = $this->getIdentity($userEntity);

        $acl = new ACL($em, $idendity);

        $this->assertTrue($acl->access('test'));
        $this->assertFalse($acl->access('test2'));
    }
}
