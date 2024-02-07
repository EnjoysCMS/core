<?php

namespace EnjoysCMS\Core\Users\Repository;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Users\Entity\Group;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<Group> findAll()
 * @method list<Group> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends EntityRepository
{
    public function getListGroupsForSelectForm(): array
    {
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
