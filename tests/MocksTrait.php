<?php

namespace Tests\EnjoysCMS;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
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
}
