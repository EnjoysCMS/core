<?php


namespace EnjoysCMS\Core\Entities;

use EnjoysCMS\Core\Components\AccessControl\Password;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class Users
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
    protected $name;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $login;


    /**
     * @ORM\Column(name="password", type="string", options={"default": ""})
     */
    private string $passwordHash;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private bool $editable = true;


    /**
     * @ORM\ManyToMany(targetEntity="Groups", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
    private $groups;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @param string $passwordHash
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @param string $password
     */
    public function genAdnSetPasswordHash(string $password): void
    {
        $this->passwordHash = Password::getHash($password);
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
    }


    public function getGroups()
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
    //
    //    public function getGroupNames(): array
    //    {
    //        $names = [];
    //        /** @var Groups $group */
    //        foreach ($this->getGroups() as $group) {
    //            $names[] = $group->getName();
    //        }
    //
    //        return $names;
    //    }

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

    /**
     * @param Groups $groups
     */
    public function setGroups(Groups $groups): void
    {
        if ($this->groups->contains($groups)) {
            return;
        }
        $this->groups[] = $groups;
    }

    public function removeGroups()
    {
        $this->groups = new ArrayCollection();
    }

    public function isGuest(): bool
    {
        if($this->getId() != self::GUEST_ID) {
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

    /**
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->editable;
    }

    /**
     * @param bool $editable
     */
    public function setEditable(bool $editable): void
    {
        $this->editable = $editable;
    }


}
