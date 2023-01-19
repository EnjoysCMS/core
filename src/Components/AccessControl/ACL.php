<?php

namespace EnjoysCMS\Core\Components\AccessControl;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Entities\User;
use Exception;

class ACL
{
    private User $user;


    private ObjectRepository|\EnjoysCMS\Core\Repositories\ACL|EntityRepository $aclRepository;

    /**
     * @var \EnjoysCMS\Core\Entities\ACL[]
     */
    private array $aclLists = [];

    /**
     * @throws Exception
     */
    public function __construct(private EntityManager $entityManager, Identity $identity)
    {
        $this->user = $identity->getUser();
        $this->aclRepository = $this->entityManager->getRepository(\EnjoysCMS\Core\Entities\ACL::class);
        $this->aclLists = $this->aclRepository->findAll();
    }

    public function isEmptyAclList(): bool
    {
        return empty($this->aclLists);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function access(string $action, string $comment = ''): bool
    {
        $acl = null;
        foreach ($this->aclLists as $item) {
            if ($item->getAction() === $action) {
                $acl = $item;
            }
        }

        if ($acl === null) {
            $acl = $this->addAcl($action, $comment);
        }

        if ($this->user->isAdmin()) {
            return true;
        }

        if (in_array($acl->getId(), $this->user->getAclAccessIds())) {
            return true;
        }
        return false;
    }

    public function getAcl(string $action): ?\EnjoysCMS\Core\Entities\ACL
    {
        return $this->aclRepository->findAcl($action);
    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function addAcl(string $action, string $comment = '', bool $flush = true): \EnjoysCMS\Core\Entities\ACL
    {
        $new = false;
        $acl = $this->getAcl($action);

        if ($acl === null) {
            $acl = new \EnjoysCMS\Core\Entities\ACL();
            $acl->setAction($action);
            $new = true;
        }

        $acl->setComment($comment === '' ? $action : $comment);
        $this->entityManager->persist($acl);

        if ($flush) {
            $this->entityManager->flush();
        }

        if ($new === true) {
            $this->aclLists[] = $acl;
        }

        return $acl;
    }
}
