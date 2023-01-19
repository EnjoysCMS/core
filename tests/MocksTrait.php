<?php

namespace Tests\EnjoysCMS;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\Auth\Authorize;
use EnjoysCMS\Core\Components\Auth\AuthorizedData;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Components\Auth\StrategyInterface;
use EnjoysCMS\Core\Entities\ACL;
use EnjoysCMS\Core\Entities\User;
use Psr\Container\ContainerInterface;

trait MocksTrait
{

    private function getEntityManagerMock()
    {
        return $this->getMockBuilder(EntityManager::class)->disableOriginalConstructor()->getMock();
    }


    private function getContainerInterfaceMock()
    {
        return $this->getMockBuilder(ContainerInterface::class)->disableOriginalConstructor()->getMock();
    }

    private function getUserEntityMock()
    {
        return $this->getMockBuilder(User::class)->disableOriginalConstructor()->getMock();
    }

    private function getAclEntityMock()
    {
        return $this->getMockBuilder(ACL::class)->disableOriginalConstructor()->getMock();
    }

    private function getAclRepositoryMock()
    {
        return $this->getMockBuilder(\EnjoysCMS\Core\Repositories\ACL::class)->disableOriginalConstructor()->getMock();
    }

    private function getEntityRepositoryMock()
    {
        return $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor()->getMock();
    }

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

    private function getACL(User $userEntity, array $aclEntities): \EnjoysCMS\Core\Components\AccessControl\ACL
    {
        $idendity = $this->getIdentity($userEntity);

        $aclRepository = $this->getAclRepositoryMock();
        $aclRepository->method('findAll')->willReturn($aclEntities);
        $aclRepository->method('findAcl')->willReturn($this->getAclEntityMock());

        $em = $this->getEntityManagerMock();
        $em->method('getRepository')->willReturn($aclRepository);
        return new \EnjoysCMS\Core\Components\AccessControl\ACL($em, $idendity);
    }
}
