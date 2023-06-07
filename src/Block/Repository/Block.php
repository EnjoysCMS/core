<?php

namespace EnjoysCMS\Core\Block\Repository;

use Doctrine\ORM\EntityRepository;

class Block extends EntityRepository
{
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        return parent::find($id, $lockMode, $lockVersion) ?? $this->findOneBy([
           'alias' => $id
        ]);
    }
}
