<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Repositories;


use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\Users;

class TokenRepository extends EntityRepository
{

    /**
     * gcProbability int 10 the probability (parts per million) that garbage collection (GC) should be performed
     *      when storing a piece of data in the cache. Defaults to 10, meaning 0.001% chance.
     *      This number should be between 0 and 1000000. A value 0 means no GC will be performed at all.
     */
    private int $gcProbability = 10;

    public function clearInactiveTokensByUser(Users $user)
    {
        $this->gc();

        $qb = $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.exp < :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;
        $qb->getQuery()->getResult();
    }


    public function clearAllInactiveTokens()
    {
        $qb = $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->andWhere('t.exp < :now')
            ->setParameter('now', new \DateTimeImmutable())
        ;
        $qb->getQuery()->getResult();
    }

    /**
     * @throws \Exception
     */
    private function gc()
    {
        if (\random_int(0, 1000000) < $this->gcProbability) {
            $this->clearAllInactiveTokens();
        }
    }
}