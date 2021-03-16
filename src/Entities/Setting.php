<?php


namespace App\Entities;


use App\Module\Admin\Components\DBAL\EnumSettingAllowedType;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping as ORM;

//Type::addType('allowedSettingType', EnumSettingAllowedType::class);

/**
 * @ORM\Entity(repositoryClass="App\Repositories\Setting")
 * @ORM\Table(name="setting")
 */
class Setting
{
    /**
     * @ORM\Column(type="string", unique=true)
     * @ORM\Id
     * @var string
     */
    private string $var;

    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $value = null;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $type;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $params;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description = null;


    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getParams(): ?string
    {
        return $this->params;
    }

    /**
     * @param string $params
     */
    public function setParams(string $params): void
    {
        $this->params = $params;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getVar(): string
    {
        return $this->var;
    }

    /**
     * @param string $var
     */
    public function setVar(string $var): void
    {
        $this->var = $var;
    }

}