<?php


namespace EnjoysCMS\Core\Components\AccessControl;


use EnjoysCMS\Core\Entities\Users;
use Doctrine\ORM\EntityManager;

class ACL
{
    private Users $user;


    /**
     * @var \App\Repositories\ACL
     */
    private $aclRepository;
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var array|object[]
     */
    private array $aclLists = [];

    public function __construct(EntityManager $entityManager, \EnjoysCMS\Core\Components\Auth\Identity $identity)
    {
        $this->entityManager = $entityManager;
        $this->user = $identity->getUser();
        $this->aclRepository = $this->entityManager->getRepository(\EnjoysCMS\Core\Entities\ACL::class);
        $this->aclLists = $this->aclRepository->findAll();
    }

    public function access(string $action, string $comment = ''): bool
    {
        $acl = null;
        foreach ($this->aclLists as $item) {
            if($item->getAction() === $action) {
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

    public function getAcl(string $action)
    {
        //        return $this->aclRepository->findOneBy(['action' => $action]);
        return $this->aclRepository->findAcl($action);
    }

    public function addAcl(string $action, string $comment = '')
    {
        $acl = new \EnjoysCMS\Core\Entities\ACL();
        $acl->setAction($action);
        $acl->setComment($comment);
        $this->entityManager->persist($acl);
        $this->entityManager->flush();

        //after added acl, reload aclList for disable multiple insert
        $this->aclLists = $this->aclRepository->findAll();

        return $acl;
        //        return $this->getAcl($action);
    }

}
