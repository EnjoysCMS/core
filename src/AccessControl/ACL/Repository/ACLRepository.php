<?php

namespace EnjoysCMS\Core\AccessControl\ACL\Repository;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\AccessControl\ACL\Entity\ACLEntity;
use EnjoysCMS\Core\Users\Entity\Group;

class ACLRepository extends EntityRepository
{
    public function findAcl(string $action): ?ACLEntity
    {
        return $this->findOneBy(['action' => $action]);
    }

    public function findByGroup($group): array
    {
        return $this->createQueryBuilder('acl')
            ->select('acl')
            ->innerJoin('acl.groups', 'g')
            ->where('g.id = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult();
    }

}
