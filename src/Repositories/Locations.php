<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Entities\Location;

class Locations extends EntityRepository
{
    public function getListLocationsForSelectForm(): array
    {
        $locations = $this->findBy([], ['name' => 'desc']);
        $ret = [];

        /** @var Location $location */
        foreach ($locations as $location) {
            $ret[' ' . $location->getId()] = $location->getName() ?? $location->getLocation();
        }

        return $ret;
    }
}
