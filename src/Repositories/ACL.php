<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;

class ACL extends EntityRepository
{
    public function findAcl(string $action): ?\EnjoysCMS\Core\Entities\ACL
    {
        return $this->findOneBy(['action' => $action]);
    }


}
