<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Repository;

use Doctrine\ORM\EntityRepository;
use Ramsey\Uuid\Uuid;

/**
 * @method \EnjoysCMS\Core\Block\Entity\Block|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<\EnjoysCMS\Core\Block\Entity\Block> findAll()
 * @method list<\EnjoysCMS\Core\Block\Entity\Block> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
