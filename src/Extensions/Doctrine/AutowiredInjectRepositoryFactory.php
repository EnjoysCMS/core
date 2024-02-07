<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Extensions\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class AutowiredInjectRepositoryFactory
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function getRepository(string $entityName): EntityRepository
    {
        return $this->em->getRepository($entityName);
    }
}
