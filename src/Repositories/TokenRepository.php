<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Repositories;


use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\Users;

class TokenRepository extends EntityRepository
{

    public function clearInactiveTokensByUser(Users $user)
    {

        $qb = $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.exp < :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;
        $qb->getQuery()->getResult();
    }
}