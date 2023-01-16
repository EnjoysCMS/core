<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Entities;

use Doctrine\Common\Collections\Collection;
use EnjoysCMS\Core\Components\Blocks\Custom;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="blocks")
 */
class Block
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     */
    private ?string $alias = null;


    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     */
    private ?string $class = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $body = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $options = null;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $cacheTtl = 0;

    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private int $status = 1;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $removable = false;


    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $cloned = false;


    /**
     * @ORM\ManyToMany(targetEntity="Location")
     * @ORM\JoinTable(
     *     name="blocks_locations",
     *     joinColumns={@ORM\JoinColumn(name="block_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id")}
     * )
     */
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function removeLocations(): void
    {
        $this->locations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        $this->alias = trim($alias);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getClass(): ?string
    {
        return $this->class ?? Custom::class;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


    public function getLocations(): Collection
    {
        return $this->locations;
    }

    public function getLocationsIds(): array
    {
        $ids = [];
        foreach ($this->getLocations() as $location) {
            $ids[] = $location->getId();
        }

        return $ids;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }


    public function getBlockActionAcl(): string
    {
        return "{$this->getClass()}::view({$this->getId()})";
    }

    public function getBlockCommentAcl(): string
    {
        return ":Блок: Доступ к просмотру блока '{$this->getName()}'";
    }


    public function getTwigTemplateString(bool $alias = false): string
    {
        if ($alias === true) {
            return "{{ ViewBlock('{$this->getAlias()}') }}";
        }
        return "{{ ViewBlock({$this->getId()}) }}";
    }

    public function isRemovable(): bool
    {
        return $this->removable;
    }

    public function setRemovable(bool $removable): void
    {
        $this->removable = $removable;
    }

    public function isCloned(): bool
    {
        return $this->cloned;
    }

    public function setCloned(bool $cloned): void
    {
        $this->cloned = $cloned;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    public function getOptionsKeyValue(): array
    {
        if (null === $options = $this->getOptions()) {
            return [];
        }
        $ret = [];
        foreach ($options as $key => $option) {
            $ret[$key] = $option['value'];
        }
        return $ret;
    }

    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function setCacheTtl(int $cacheTtl): void
    {
        $this->cacheTtl = $cacheTtl;
    }

    public function setLocations(Location $location): void
    {
        $this->locations[] = $location;
    }
}
