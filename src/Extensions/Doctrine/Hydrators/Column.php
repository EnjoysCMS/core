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
        return $this->_stmt?->fetchFirstColumn() ?? [];
    }
}
