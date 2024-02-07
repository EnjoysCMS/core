<?php

namespace EnjoysCMS\Core\AccessControl;

use EnjoysCMS\Core\Users\Entity\Group;

class AccessControl
{

    public function __construct(private readonly AccessControlManage $manage)
    {
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
