<?php

namespace EnjoysCMS\Core\AccessControl;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\AccessControl\ACL\ACLManage;
use EnjoysCMS\Core\Users\Entity\Group;

class AccessControl
{


    private AccessControlManage $manage;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(Container $container)
    {
        $this->manage = $container->get(ACLManage::class);
    }

    public function getManage(): AccessControlManage
    {
        return $this->manage;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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
