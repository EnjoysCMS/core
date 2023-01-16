<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;

class Group extends EntityRepository
{
    public function getListGroupsForSelectForm(): array
    {
        $groups = $this->findAll();
        $ret = [];

        /** @var \EnjoysCMS\Core\Entities\Group $group */
        foreach ($groups as $group) {
            $ret[' ' . $group->getId()] = $group->getName();
        }

        return $ret;
    }

    public function getGroupsArray(): array
    {
        $groupsArray = [];
        $groups = $this->findAll();
        foreach ($groups as $group) {
            $groupsArray[$group->getId() . ' '] = $group->getName();
        }
        return $groupsArray;
    }
}
