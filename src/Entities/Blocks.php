<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Entities;

use EnjoysCMS\Core\Components\Blocks\Custom;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Blocks
 * @package App\Modules\System\Entities
 * @ORM\Entity
 * @ORM\Table(name="blocks")
 */
class Blocks
{
    /**
     * @var                        int ID
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     */
    private ?string $alias = null;


    /**
     * @var                       string
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var                       string|null
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     */
    private ?string $class = null;

    /**
     * @var                     string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $body = null;

    /**
     * @var                     array|null
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $options = null;

    /**
     * @var                        int
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $cacheTtl = 0;

    /**
     * @var                        int
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private int $status = 1;

    /**
     * @var                        bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $removable = false;


    /**
     * @var                        bool
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private bool $cloned = false;


    /**
     * @ORM\ManyToMany(targetEntity="Locations")
     * @ORM\JoinTable(
     *     name="blocks_locations",
     *     joinColumns={@ORM\JoinColumn(name="block_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="location_id", referencedColumnName="id")}
     * )
     */
    private $locations;

    public function __construct()
    {
        $this->locations = new ArrayCollection();
    }

    public function removeLocations()
    {
        $this->locations = new ArrayCollection();
    }

    /**
     * @return int
     */
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
        $this->alias = $alias;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class ?? Custom::class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


    public function getLocations()
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

    /**
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * @param string|null $body
     */
    public function setBody(?string $body): void
    {
        $this->body = $body;
    }


    public function getBlockActionAcl()
    {
        return "{$this->getClass()}::view({$this->getId()})";
    }

    public function getBlockCommentAcl()
    {
        return ":Блок: Доступ к просмотру блока '{$this->getName()}'";
    }


    public function getTwigTemplateString()
    {
        return "{{ ViewBlock({$this->getId()}) }}";
    }

    /**
     * @return bool
     */
    public function isRemovable(): bool
    {
        return $this->removable;
    }

    /**
     * @param bool $removable
     */
    public function setRemovable(bool $removable): void
    {
        $this->removable = $removable;
    }

    /**
     * @return bool
     */
    public function isCloned(): bool
    {
        return $this->cloned;
    }

    /**
     * @param bool $cloned
     */
    public function setCloned(bool $cloned): void
    {
        $this->cloned = $cloned;
    }

    /**
     * @return array|null
     */
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

    /**
     * @param array|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    /**
     * @return int
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    /**
     * @param int $cacheTtl
     */
    public function setCacheTtl(int $cacheTtl): void
    {
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param Locations $location
     */
    public function setLocations(Locations $location): void
    {
        $this->locations[] = $location;
    }
}
