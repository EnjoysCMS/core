<?php

namespace Tests\EnjoysCMS\Components\AccessControl;

use Doctrine\ORM\EntityManager;
use EnjoysCMS\Core\Components\AccessControl\ACL;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Entities\User;
use PHPUnit\Framework\TestCase;
use Tests\EnjoysCMS\TestHelper;
use Tests\EnjoysCMS\Traits\MockHelper;

class ACLTest extends TestCase
{
    use MockHelper;

    /**
     * @throws \Exception
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

    public function testAccess()
    {
        $acl = $this->getAclImpl(
            [
                'test' => 'comment',
                'test2' => 'comment',
            ],
            [
                1
            ]
        );

        $this->assertTrue($acl->access('test'));
        $this->assertFalse($acl->access('test2'));
    }
}
