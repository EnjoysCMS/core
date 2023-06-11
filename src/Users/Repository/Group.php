<?php

namespace EnjoysCMS\Core\Users\Repository;

use Doctrine\ORM\EntityRepository;

class Group extends EntityRepository
{
    public function getListGroupsForSelectForm(): array
    {
        /** @var \EnjoysCMS\Core\Users\Entity\Group[] $groups */
        $groups = $this->findAll();
        $ret = [];

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
