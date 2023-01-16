<?php

namespace EnjoysCMS\Core\Entities;

use Doctrine\Common\Collections\Collection;
use EnjoysCMS\Core\Components\AccessControl\Password;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    public const ADMIN_GROUP_ID = 1;
    public const GUEST_ID = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected string $login;

    /**
     * @ORM\Column(name="password", type="string", options={"default": ""})
     */
    private string $passwordHash;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private bool $editable = true;


    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
    private Collection $groups;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId()
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function genAndSetPasswordHash(string $password): void
    {
        $this->passwordHash = Password::getHash($password);
    }

    /**
     * @deprecated use genAndSetPasswordHash()
     * remove in 4.5
     */
    public function genAdnSetPasswordHash(string $password): void
    {
        $this->genAndSetPasswordHash($password);
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }


    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function getGroupIds(): array
    {
        $ids = [];
        foreach ($this->getGroups() as $group) {
            $ids[] = $group->getId();
        }

        return $ids;
    }

    public function getAclAccessIds(): array
    {
        $ids = [];
        foreach ($this->getGroups() as $group) {
            foreach ($group->getAcl() as $acl) {
                $ids[] = $acl->getId();
            }
        }

        return array_unique($ids);
    }

    public function setGroups(Group $groups): void
    {
        if ($this->groups->contains($groups)) {
            return;
        }
        $this->groups[] = $groups;
    }

    public function removeGroups(): void
    {
        $this->groups = new ArrayCollection();
    }

    public function isGuest(): bool
    {
        if ($this->getId() != self::GUEST_ID) {
            return false;
        }
        return true;
    }

    public function isAdmin(): bool
    {
        if (in_array(self::ADMIN_GROUP_ID, $this->getGroupIds())) {
            return true;
        }
        return false;
    }

    public function isUser(): bool
    {
        return  !$this->isGuest();
    }

    public function isEditable(): bool
    {
        return $this->editable;
    }

    public function setEditable(bool $editable): void
    {
        $this->editable = $editable;
    }
}
