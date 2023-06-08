<?php

namespace EnjoysCMS\Core\Block\Repository;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

class Block extends EntityRepository
{
    public function find($id, $lockMode = null, $lockVersion = null): ?\EnjoysCMS\Core\Block\Entity\Block
    {
        if (!Uuid::isValid($id)) {
            return $this->findOneBy([
                'alias' => $id
            ]);
        }
        return parent::find($id, $lockMode, $lockVersion) ?? $this->findOneBy([
            'alias' => $id
        ]);
    }
}
