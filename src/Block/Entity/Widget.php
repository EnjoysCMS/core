<?php

namespace EnjoysCMS\Core\Block\Entity;

use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Block\Options;
use EnjoysCMS\Core\Block\Repository\Widgets;
use EnjoysCMS\Core\Users\Entity\User;

#[ORM\Entity(repositoryClass: Widgets::class)]
#[ORM\Table(name: 'widgets')]
class Widget
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true, options: ['default' => null])]
    private ?string $class = null;

    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    private iterable $options = [];

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $cacheTtl = 0;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

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

    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }

    public function setCacheTtl(int $cacheTtl): void
    {
        $this->cacheTtl = $cacheTtl;
    }

    public function getWidgetActionAcl(): string
    {
        return "{$this->getClass()}::view({$this->getId()})";
    }

    public function getWidgetCommentAcl(): string
    {
        return ":Widget: Доступ к просмотру блока '{$this->getName()}'";
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
}
