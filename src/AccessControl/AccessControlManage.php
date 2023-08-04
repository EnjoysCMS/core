<?php

namespace EnjoysCMS\Core\AccessControl;

use EnjoysCMS\Core\Users\Entity\Group;

interface AccessControlManage
{
    public function isAccess(string $action): bool;

    public function register(string $action, ?string $comment = null, bool $flush = true);

    public function getAccessAction(string $action);
    /**
     * @return Group[]
     */
    public function getAuthorizedGroups(string $action): array;

    public function getAccessActionsForGroup($group);

    public function getList();
}
