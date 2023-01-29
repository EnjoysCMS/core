<?php

namespace EnjoysCMS\Core\Components\Doctrine\Hydrators;

use Doctrine\DBAL\Exception\NoKeyValue;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class KeyPair extends AbstractHydrator
{
    /**
     * @return array
     * @throws NoKeyValue
     */
    protected function hydrateAllData(): array
    {

        $columnCount = $this->_stmt->columnCount();

        if ($columnCount < 2) {
            throw NoKeyValue::fromColumnCount($columnCount);
        }

        $data = [];
        foreach ($this->_stmt->fetchAllNumeric() as [$key, $value]) {
            $data[$key] = $value;
        }
        return $data;
    }
}
