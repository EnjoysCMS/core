<?php

namespace EnjoysCMS\Core\AccessControl;

use EnjoysCMS\Core\AccessControl\ACL\ACLManage;
use EnjoysCMS\Core\Users\Entity\Group;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class AccessControl
{


    private AccessControlManage $manage;


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->manage = $container->get(ACLManage::class);
    }

    public function getManage(): AccessControlManage
    {
        return $this->manage;
    }


    public function isAccess(string $task): bool
    {
        return $this->getManage()->isAccess($task);
    }


    /**
     * @return Group[]
     */
    public function getAuthorizedGroups(string $action): array
    {
        return $this->getManage()->getAuthorizedGroups($action);
    }
}
