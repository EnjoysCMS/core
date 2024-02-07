<?php

namespace EnjoysCMS\Core\AccessControl\ACL;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\AccessControl\AccessControlManage;
use EnjoysCMS\Core\AccessControl\ACL\Entity\ACLEntity;
use EnjoysCMS\Core\AccessControl\ACL\Repository\ACLRepository;
use EnjoysCMS\Core\Auth\Identity;
use EnjoysCMS\Core\Users\Entity\Group;
use Exception;

class ACLManage implements AccessControlManage
{

    /**
     * @var ACLEntity[]
     */
    private array $aclList;

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Identity $identity,
        private readonly ACLRepository $aclRepository,
    ) {
        $this->aclList = $this->getList();
    }

    public function isEmptyAclList(): bool
    {
        return empty($this->getList());
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws Exception
     */
    public function isAccess(string $action): bool
    {
        $user = $this->identity->getUser();
        $acl = null;

        if ($user->isAdmin()) {
            return true;
        }

        foreach ($this->aclList as $item) {
            if ($item->getAction() === $action) {
                $acl = $item;
                break;
            }
        }

        if ($acl === null) {
            return false;
        }

        return !$user->getGroups()->filter(
            fn($el) => in_array($el, $acl->getGroups()->getValues())
        )->isEmpty();
    }

    public function getAccessAction(string $action): ?ACLEntity
    {
        return $this->aclRepository->findAcl($action);
    }


     public function register(
        string $action,
        ?string $comment = null,
        bool $flush = true
    ): ACLEntity {
        $acl = $this->getAccessAction($action) ?? new ACLEntity();
        $acl->setAction($action);
        $acl->setComment($comment);
        $this->em->persist($acl);
        if ($flush) {
            $this->em->flush();
        }
        return $acl;
    }

    /**
     * @return Group[]
     */
    public function getAuthorizedGroups(string $action): array
    {
        return $this->getAccessAction($action)?->getGroups()->toArray() ?? [];
    }

    public function getAccessActionsForGroup($group): array
    {
        return $this->aclRepository->findByGroup($group);
    }

    public function getList(): array
    {
        return $this->aclRepository->findAll();
    }
}
