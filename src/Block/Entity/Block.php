<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Block\AbstractBlock;
use EnjoysCMS\Core\Block\Options;
use EnjoysCMS\Core\Entities\Location;

#[ORM\Entity(repositoryClass: \EnjoysCMS\Core\Block\Repository\Block::class)]
#[ORM\Table(name: 'blocks')]
class Block
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\Column(type: 'uuid')]
    private string $id;

    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: true, options: ['default' => null])]
    private ?string $alias = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    /**
     * @var class-string<AbstractBlock>
     */
    #[ORM\Column(type: 'string')]
    private string $className;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;

    #[ORM\Column(type: 'json', options: ['default' => []])]
    private iterable $options = [];

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $cacheTtl = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $status = 1;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $removable = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $cloned = false;

    #[ORM\JoinTable(name: 'blocks_locations')]
    #[ORM\JoinColumn(name: 'block_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'location_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Location::class)]
    private Collection $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function removeLocations(): void
    {
        $this->locations = new ArrayCollection();
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(?string $alias): void
    {
        if ($alias === null) {
            $this->alias = null;
            return;
        }

        if (empty($alias = trim($alias))) {
            $this->alias = null;
            return;
        }
        $this->alias = $alias;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return class-string<AbstractBlock>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param class-string<AbstractBlock> $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
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

    /**
     * @return int[]
     */
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
        return "{$this->getClassName()}::view({$this->getId()})";
    }

    public function getBlockCommentAcl(): string
    {
        return ":Блок: Доступ к просмотру блока '{$this->getName()}'";
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

    public function getOptions(): Options
    {
        return Options::createFromArray($this->options);
    }

    public function getOptionsKeyValue(): array
    {
        $ret = [];
        foreach ($this->getOptions() as $key => $option) {
            $ret[$key] = $option['value'];
        }
        return $ret;
    }

    public function setOptions(iterable $options): void
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
