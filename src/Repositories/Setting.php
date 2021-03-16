<?php


namespace App\Repositories;


use App\Components\Helpers\Config;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Internal\Hydration\ArrayHydrator;
use Doctrine\ORM\Mapping\Cache;

class Setting extends EntityRepository
{

    public function findAllKeyVar()
    {
        return $this->createQueryBuilder('s')
            ->select('s.var, s.value')
            ->getQuery()
            ->enableResultCache(Config::get('orm', 'setting-cachetime', 0))
            ->getResult('KeyPair')
            ;
    }
}