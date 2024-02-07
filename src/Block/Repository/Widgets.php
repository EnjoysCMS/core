<?php

namespace EnjoysCMS\Core\Block\Repository;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Block\Entity\Widget;
use EnjoysCMS\Core\Users\Entity\User;

/**
 * @method Widget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Widget|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<Widget> findAll()
 * @method list<Widget> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Widgets extends EntityRepository
{
    /**
     * @return list<Widget>
     */
    public function getByUser(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }
}
