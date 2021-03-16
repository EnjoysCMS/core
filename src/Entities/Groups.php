<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class UsersGroup
 * @package App\Components\Entities
 * @ORM\Entity(repositoryClass="App\Repositories\Groups")
 * @ORM\Table(name="`groups`")
 */
class Groups
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="integer", options={"default": 1})
     */
    private int $status;
    /**
     * @ORM\Column(name="`system`", type="boolean", options={"default": false})
     */
    private bool $system = false;

    /**
     * @ORM\ManyToMany(targetEntity="Users", mappedBy="groups")
     */
    private $users;

    /**
     * @ORM\ManyToMany(targetEntity="ACL", inversedBy="acl")
     * @ORM\JoinTable(name="acl_groups")
     */
    private $acl;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->acl = new ArrayCollection();
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


    /**
     * @return ArrayCollection
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param ACL $acl
     */
    public function setAcl(ACL $acl): void
    {
        if ($this->acl->contains($acl)) {
            return;
        }
        $this->acl->add($acl);
        $acl->setGroups($this);
    }

    public function removeAcl(?ACL $acl = null)
    {
        if($acl === null){
            $this->acl->clear();
            return;
        }

        if(!$this->acl->contains($acl)){
            return;
        }

        $this->acl->removeElement($acl);
        $acl->removeGroups($this);

    }

    /**
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->system;
    }

    /**
     * @param bool $system
     */
    public function setSystem(bool $system): void
    {
        $this->system = $system;
    }




}
