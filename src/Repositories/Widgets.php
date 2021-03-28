<?php


namespace EnjoysCMS\Core\Repositories;


use Doctrine\ORM\EntityRepository;

class Widgets extends EntityRepository
{

    public function getSortWidgets()
    {
        return $this->findAll();
    }

}