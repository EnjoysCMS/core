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
    private $acl;
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @var array|object[]
     */
    private array $aclLists = [];

    public function __construct(EntityManager $entityManager, Identity $identity)
    {
        $this->entityManager = $entityManager;
        $this->user = $identity->getUser();
        $this->acl = $this->entityManager->getRepository(\EnjoysCMS\Core\Entities\ACL::class);
        $this->aclLists = $this->acl->findAll();
    }

    public function access(string $action, string $comment = ''): bool
    {
        $acl = null;
        foreach ($this->aclLists as $item) {
            if($item->getAction() === $action){
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
        return $this->acl->findAcl($action);
    }

    public function addAcl(string $action, string $comment = '')
    {
        $acl = new \EnjoysCMS\Core\Entities\ACL();
        $acl->setAction($action);
        $acl->setComment($comment);
        $this->entityManager->persist($acl);
        $this->entityManager->flush();
        return $acl;
//        return $this->getAcl($action);
    }

}
