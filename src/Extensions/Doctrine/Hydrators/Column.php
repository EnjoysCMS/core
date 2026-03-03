<?php

namespace EnjoysCMS\Core\Extensions\Doctrine\Hydrators;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

/**
 * @psalm-suppress UndefinedThisPropertyFetch
 */
class Column extends AbstractHydrator
{
    /**
     * @throws Exception
     */
    #[\Override]
    protected function hydrateAllData(): array
    {
        $stmt = $this->stmt ?? $this->_stmt ?? null;
        return $stmt?->fetchFirstColumn() ?? [];
    }
}
