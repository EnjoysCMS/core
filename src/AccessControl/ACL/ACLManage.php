<?php

namespace EnjoysCMS\Core\AccessControl\ACL;

use Doctrine\ORM\EntityManager;
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
    private ACLRepository|EntityRepository $aclRepository;

    private array $aclList;
    /**
     * @throws Exception
     */
    public function __construct(
        private readonly EntityManager $em,
        private readonly Identity $identity
    ) {
        $this->aclRepository = $this->em->getRepository(ACLEntity::class);
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

        foreach ($this->aclList as $item) {

            if ($item->getAction() === $action) {
                $acl = $item;
                break;
            }
        }
//        dd($acl);
//        dd($this->aclLists);
//        if ($acl === null) {
//            $acl = $this->register($route, $controller, $comment);
//        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($acl === null){
            return false;
        }

//        dd($user->getGroups());
        if (in_array($acl->getId(), [])) {
            return true;
        }
        return false;
    }

    public function getAccessAction(string $action): ?ACLEntity
    {
        return $this->aclRepository->findAcl($action);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
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

    public function getAccessActionsForGroup($group)
    {
        return $this->aclRepository->findByGroup($group);
    }

    public function getList(): array
    {
        return $this->aclRepository->findAll();
    }
}
