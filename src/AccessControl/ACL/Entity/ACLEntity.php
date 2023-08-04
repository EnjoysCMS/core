<?php

namespace EnjoysCMS\Core\AccessControl\ACL\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\AccessControl\ACL\Repository\ACLRepository;
use EnjoysCMS\Core\Users\Entity\Group;

#[ORM\Entity(repositoryClass: ACLRepository::class)]
#[ORM\Table(name: 'acl')]
class ACLEntity
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $action;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToMany(targetEntity: Group::class)]
    private Collection $groups;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }



    public function getId(): int
    {
        return $this->id;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }


    public function removeGroup(Group $group): void
    {
        if (!$this->groups->contains($group)) {
            return;
        }

        $this->groups->removeElement($group);
    }

    public function addGroup(Group $group): void
    {
        if ($this->groups->contains($group)) {
            return;
        }

        $this->groups->add($group);
    }

    public function setGroups(array|Collection $groups): void
    {
        $this->groups = new ArrayCollection($groups);
    }

    public function clearGroups(): void
    {
        $this->groups->clear();
    }


    public function getGroups(): Collection
    {
        return $this->groups;
    }

}
