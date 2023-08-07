<?php

namespace EnjoysCMS\Core\Users\Entity;

use Doctrine\Common\Collections\Collection;
use EnjoysCMS\Core\AccessControl\Password;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Users\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User
{
    public const ADMIN_GROUP_ID = 1;
    public const GUEST_ID = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'string', unique: true)]
    protected string $login;

    #[ORM\Column(name: 'password', type: 'string', options: ['default' => ''])]
    private string $passwordHash;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $editable = true;

    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'users_groups')]
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
        $this->setPasswordHash(Password::getHash($password));
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
