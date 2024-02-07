<?php

namespace EnjoysCMS\Core\Block\Repository;

use EnjoysCMS\Core\Block\Entity\BlockLocation;
use Doctrine\ORM\EntityRepository;

/**
 * @method BlockLocation|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockLocation|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockLocation[] findAll()
 * @method BlockLocation[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockLocationRepository extends EntityRepository
{
}
