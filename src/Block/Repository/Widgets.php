<?php

namespace EnjoysCMS\Core\Block\Repository;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Users\Entity\User;

class Widgets extends EntityRepository
{
    public function getSortWidgets(User $user): array
    {
        return $this->findBy(['user' => $user]);
    }
}
