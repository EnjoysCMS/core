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
    private string $action;

    #[ORM\Column(type: 'string')]
    private string $comment;

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

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }
}
