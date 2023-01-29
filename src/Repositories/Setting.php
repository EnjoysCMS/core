<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;

class Setting extends EntityRepository
{
    public function findAllKeyVar()
    {
        return $this->createQueryBuilder('s')
            ->select('s.var, s.value')
            ->getQuery()
            //->enableResultCache(\EnjoysCMS\Core\Components\Helpers\Config::get('orm', 'setting-cachetime', 0))
            ->getResult('KeyPair');
    }
}
