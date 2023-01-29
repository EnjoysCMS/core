<?php

namespace EnjoysCMS\Core\Repositories;

use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Components\Helpers\Blocks;
use EnjoysCMS\Core\Components\Helpers\Routes;

class ACL extends EntityRepository
{
    public function findAcl(string $action): ?\EnjoysCMS\Core\Entities\ACL
    {
        return $this->findOneBy(['action' => $action]);
    }

    public function getAllActiveACL(): array
    {
        $allActiveControllers = Routes::getAllActiveControllers();
        $allActiveBlocksController = Blocks::getActiveBlocksController();

        $allAcl = $this->findAll();
        /** @var \EnjoysCMS\Core\Entities\ACL $acl */
        foreach ($allAcl as $key => $acl) {
            if (!in_array($acl->getAction(), array_merge($allActiveControllers, $allActiveBlocksController))) {
                unset($allAcl[$key]);
                $this->getEntityManager()->remove($acl);
                $this->getEntityManager()->flush();
            }
        }

        return $allAcl;
    }

    public function synchronizeActives(): void
    {
        $this->getAllActiveACL();
    }
}
