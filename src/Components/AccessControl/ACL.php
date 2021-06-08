<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ObjectRepository;
use EnjoysCMS\Core\Components\Auth\Identity;
use EnjoysCMS\Core\Entities\User;

class ACL
{
    private User $user;


    private ObjectRepository|\EnjoysCMS\Core\Repositories\ACL|EntityRepository $aclRepository;

    /**
     * @var array|object[]
     */
    private array $aclLists = [];

    /**
     * @throws \Exception
     */
    public function __construct(private EntityManager $entityManager, Identity $identity)
    {
        $this->user = $identity->getUser();
        $this->aclRepository = $this->entityManager->getRepository(\EnjoysCMS\Core\Entities\ACL::class);
        $this->aclLists = $this->aclRepository->findAll();
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
    public function addAcl(string $action, string $comment = ''): \EnjoysCMS\Core\Entities\ACL
    {
        $acl = new \EnjoysCMS\Core\Entities\ACL();
        $acl->setAction($action);
        $acl->setComment($comment);
        $this->entityManager->persist($acl);
        $this->entityManager->flush();

        //after added acl, reload aclList for disable multiple insert
        $this->aclLists = $this->aclRepository->findAll();

        return $acl;
    }

}
