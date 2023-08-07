<?php

namespace EnjoysCMS\Core\Block\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'locations')]
class BlockLocation
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $location;


    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
