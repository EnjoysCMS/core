<?php


namespace EnjoysCMS\Core\Entities;

use EnjoysCMS\Core\Components\Blocks\Custom;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Widgets
 * @package EnjoysCMS\Core\Entities
 * @ORM\Entity(repositoryClass="EnjoysCMS\Core\Repositories\Widgets")
 * @ORM\Table(name="widgets")
 */
class Widgets
{
    /**
     * @var                        int ID
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
        return $this->class;
    }

    /**
     * @param string|null $class
     */
    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return array|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
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

    public function getWidgetActionAcl()
    {
        return "{$this->getClass()}::view({$this->getId()})";
    }

    public function getWidgetCommentAcl()
    {
        return ":Widget: Доступ к просмотру блока '{$this->getName()}'";
    }



}
