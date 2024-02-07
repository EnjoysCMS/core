<?php

namespace EnjoysCMS\Core\Setting\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * @method \EnjoysCMS\Core\Setting\Entity\Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method \EnjoysCMS\Core\Setting\Entity\Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<\EnjoysCMS\Core\Setting\Entity\Setting> findAll()
 * @method list<\EnjoysCMS\Core\Setting\Entity\Setting> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
