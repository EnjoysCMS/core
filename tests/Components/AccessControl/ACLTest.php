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



    public function testAccess()
    {
        $userEntity = $this->getUserEntityMock();

        $userEntity->method('getAclAccessIds')->willReturn([
            1
        ]);

        $aclEntity = $this->getAclEntityMock();
        $aclEntity->method('getId')->willReturn(1);
        $aclEntity->method('getAction')->willReturn('test');


        $acl = $this->getACL($userEntity, [$aclEntity]);

        $this->assertTrue($acl->access('test'));
        $this->assertFalse($acl->access('test2'));
    }
}
