<?php

namespace EnjoysCMS\Core\Setting\Entity;

use Doctrine\ORM\Mapping as ORM;

//Type::addType('allowedSettingType', EnumSettingAllowedType::class);

/**
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: \EnjoysCMS\Core\Setting\Repository\Setting::class)]
#[ORM\Table(name: 'setting')]
class Setting
{

    #[ORM\Column(type: 'string', unique: true)]
    #[ORM\Id]
    private string $var;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $value = null;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $params;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'boolean', options: ["default" => false])]
    private bool $removable = false;


    public function isRemovable(): bool
    {
        return $this->removable;
    }

    public function setRemovable(bool $removable): void
    {
        $this->removable = $removable;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getParams(): ?string
    {
        return $this->params;
    }

    public function setParams(string $params): void
    {
        $this->params = $params;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getVar(): string
    {
        return $this->var;
    }

    public function setVar(string $var): void
    {
        $this->var = $var;
    }
}
