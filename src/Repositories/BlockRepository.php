<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Entities\Block;

class BlockRepository extends EntityRepository
{
    /**
     * @param int|string $id
     * @param int|null $lockMode
     * @param int|null $lockVersion
     * @return Block|null
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?Block
    {
        return parent::find($id, $lockMode, $lockVersion)
            ?? $this->findOneBy(['alias' => $id]);
    }
}
