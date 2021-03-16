<?php


namespace EnjoysCMS\Core\Components\Doctrine\Hydrators;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class Column extends AbstractHydrator
{
    protected function hydrateAllData()
    {
        return $this->_stmt->fetchFirstColumn();
    }
}
