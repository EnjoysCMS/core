<?php

namespace EnjoysCMS\Core\Location\Entities;

use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Location\Repositories\Locations;

#[ORM\Entity(repositoryClass: Locations::class)]
#[ORM\Table(name: 'locations')]
class Location
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $location;

    #[ORM\Column(type: 'string', nullable: true, options: ['default' => null])]
    private ?string $name = null;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
