<?php

namespace EnjoysCMS\Core\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EnjoysCMS\Core\Repositories\Widgets")
 * @ORM\Table(name="widgets")
 */
class Widget
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=true, options={"default":null})
     */
    private ?string $class = null;


    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $options = null;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private int $cacheTtl = 0;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    public function getId(): int
    {
        return $this->id;
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
        return $this->class;
    }

    public function setClass(?string $class): void
    {
        $this->class = $class;
    }

    public function getOptions(): ?array
    {
        return $this->options;
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

    public function getWidgetActionAcl()
    {
        return "{$this->getClass()}::view({$this->getId()})";
    }

    public function getWidgetCommentAcl()
    {
        return ":Widget: Доступ к просмотру блока '{$this->getName()}'";
    }
}
