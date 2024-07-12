<?php

namespace EnjoysCMS\Core\Extensions\Doctrine\Hydrators;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\NoKeyValue;
use Doctrine\ORM\Internal\Hydration\AbstractHydrator;

class KeyPair extends AbstractHydrator
{
    /**
     * @return array
     * @throws NoKeyValue
     * @throws Exception
     */
    protected function hydrateAllData(): array
    {

        $stmt = $this->stmt ?? $this->_stmt ?? null;

        $columnCount = $stmt?->columnCount() ?? 0;

        if ($columnCount < 2) {
            throw NoKeyValue::fromColumnCount($columnCount);
        }

        $data = [];
        foreach ($stmt?->fetchAllNumeric() ?? [] as [$key, $value]) {
            $data[$key] = $value;
        }
        return $data;
    }
}
