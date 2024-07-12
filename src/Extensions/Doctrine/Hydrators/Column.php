<?php

namespace EnjoysCMS\Core\Extensions\Doctrine\Hydrators;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class Column extends AbstractHydrator
{
    /**
     * @throws Exception
     */
    protected function hydrateAllData(): array
    {
        $stmt = $this->stmt ?? $this->_stmt ?? null;
        return $stmt?->fetchFirstColumn() ?? [];
    }
}
