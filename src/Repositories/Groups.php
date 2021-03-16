<?php


namespace App\Repositories;


use Doctrine\ORM\EntityRepository;

class Groups extends EntityRepository
{

    public function getListGroupsForSelectForm()
    {
        $groups = $this->findAll();
        $ret = [];
        /** @var \App\Entities\Groups $group */
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