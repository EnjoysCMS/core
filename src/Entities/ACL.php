<?php


namespace EnjoysCMS\Core\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class ACL
 *
 * @package                                                       App\Modules\System\Entities
 * @ORM\Entity(repositoryClass="EnjoysCMS\Core\Repositories\ACL")
 * @ORM\Table(name="acl")
 */
class ACL
{
    /**
     * @var                        int
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var                       string
     * @ORM\Column(type="string")
     */
    private string $action;

    /**
     * @var                       string
     * @ORM\Column(type="string")
     */
    private string $comment;

    /**
     * @ORM\ManyToMany(targetEntity="Groups", mappedBy="acl")
     */
    private $groups;


    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    public function removeGroups(?Groups $groups = null)
    {
        if($groups === null) {
            $this->groups->clear();
            return;
        }

        if(!$this->groups->contains($groups)) {
            return;
        }

        $this->groups->removeElement($groups);
        $groups->removeAcl($this);
    }

    public function setGroups(Groups $groups): void
    {
        if ($this->groups->contains($groups)) {
            return;
        }

        $this->groups->add($groups);
        $groups->setAcl($this);
    }

    public function setGroupsCollection(array $groups)
    {
        $this->groups = new ArrayCollection($groups);

    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }


}
