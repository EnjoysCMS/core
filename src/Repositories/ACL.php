<?php


namespace App\Repositories;


use App\Components\Helpers\Blocks;
use App\Components\Helpers\Routes;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;

class ACL extends EntityRepository
{

    public function findAcl(string $action)
    {
        return $this->findOneBy(['action' => $action]);
    }

    /**
     * @throws ORMException
     */
    public function getAllActiveACL()
    {
        $allActiveControllers = Routes::getAllActiveControllers();
        $allActiveBlocksController = Blocks::getActiveBlocksController();

        $allAcl = $this->findAll();
        /** @var \App\Entities\ACL $acl */
        foreach ($allAcl as $key => $acl){

            if(!in_array($acl->getAction(),array_merge($allActiveControllers, $allActiveBlocksController) )){
                unset($allAcl[$key]);
                $this->getEntityManager()->remove($acl);
                $this->getEntityManager()->flush();
            }

        }

        return $allAcl;
    }

    /**
     * @throws ORMException
     */
    public function synchronizeActives(): void
    {
        $this->getAllActiveACL();
    }
}
