<?php

namespace EnjoysCMS\Core\AccessControl;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use EnjoysCMS\Core\Auth\Identity;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;

class ACL
{
    private \EnjoysCMS\Core\Repositories\ACL|EntityRepository $aclRepository;

    /**
     * @var \EnjoysCMS\Core\Entities\ACL[]
     */
    private array $aclLists = [];

    /**
     * @throws Exception
     */
    public function __construct(
        private readonly EntityManager $entityManager,
        private readonly Identity $identity
    ) {
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
     * @throws Exception
     */
    public function access(string $action, string $comment = ''): bool
    {
        $user = $this->identity->getUser();

        $acl = null;
        foreach ($this->aclLists as $item) {
            if ($item->getAction() === $action) {
                $acl = $item;
            }
        }

        if ($acl === null) {
            $acl = $this->addAcl($action, $comment);
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (in_array($acl->getId(), $user->getAclAccessIds())) {
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
