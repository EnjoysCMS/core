<?php


namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Locations
 * @package App\Modules\System\Entities
 * @ORM\Entity(repositoryClass="App\Repositories\Locations")
 * @ORM\Table(name="locations")
 */
class Locations
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $location;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, options={"default": null})
     */
    private ?string $name = null;

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

}
