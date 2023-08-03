<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;

class ACL extends EntityRepository
{
    public function findAcl(string $controller): ?\EnjoysCMS\Core\Entities\ACL
    {
        return $this->findOneBy(['controller' => $controller]);
    }


}
