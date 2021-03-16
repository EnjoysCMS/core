<?php


namespace App\Repositories;


use Doctrine\ORM\EntityRepository;

class Locations extends EntityRepository
{
    public function getListLocationsForSelectForm()
    {
        $locations = $this->findBy([], ['name' => 'desc']);
        $ret = [];
        /** @var \App\Entities\Locations $location */
        foreach ($locations as $location) {
            $ret[' ' . $location->getId()] = $location->getName() ?? $location->getLocation();
        }

        return $ret;
    }
}