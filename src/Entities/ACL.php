<?php

namespace EnjoysCMS\Core\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Users\Entity\Group;

#[ORM\Entity(repositoryClass: \EnjoysCMS\Core\Repositories\ACL::class)]
#[ORM\Table(name: 'acl')]
class ACL
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $route;

    #[ORM\Column(type: 'string')]
    private string $controller;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'acl')]
    private Collection $groups;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function removeGroups(?Group $groups = null): void
    {
        if ($groups === null) {
            $this->groups->clear();
            return;
        }

        if (!$this->groups->contains($groups)) {
            return;
        }

        $this->groups->removeElement($groups);
        $groups->removeAcl($this);
    }

    public function setGroups(Group $groups): void
    {
        if ($this->groups->contains($groups)) {
            return;
        }

        $this->groups->add($groups);
        $groups->setAcl($this);
    }

    public function setGroupsCollection(array $groups): void
    {
        $this->groups = new ArrayCollection($groups);
    }


    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }


}
