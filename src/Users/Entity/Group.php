<?php

namespace EnjoysCMS\Core\Users\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Entities\ACL;

#[ORM\Entity(repositoryClass: \EnjoysCMS\Core\Users\Repository\Group::class)]
#[ORM\Table(name: '`groups`')]
class Group
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;


    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $status;

    #[ORM\Column(name: '`system`', type: 'boolean', options: ['default' => false])]
    private bool $system = false;


    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    private Collection $users;

//    #[ORM\ManyToMany(targetEntity: ACL::class, inversedBy: 'acl')]
//    #[ORM\JoinTable(name: 'acl_groups')]
//    private Collection $acl;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->acl = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

//    public function getAcl(): Collection
//    {
//        return $this->acl;
//    }
//
//    public function setAcl(ACL $acl): void
//    {
//        if ($this->acl->contains($acl)) {
//            return;
//        }
//        $this->acl->add($acl);
//        $acl->setGroups($this);
//    }
//
//    public function removeAcl(?ACL $acl = null): void
//    {
//        if ($acl === null) {
//            $this->acl->clear();
//            return;
//        }
//
//        if (!$this->acl->contains($acl)) {
//            return;
//        }
//
//        $this->acl->removeElement($acl);
//        $acl->removeGroups($this);
//    }

    public function isSystem(): bool
    {
        return $this->system;
    }

    public function setSystem(bool $system): void
    {
        $this->system = $system;
    }
}
