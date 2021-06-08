<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Repositories;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EnjoysCMS\Core\Components\Helpers\Config;
use EnjoysCMS\Core\Entities\Token;
use EnjoysCMS\Core\Entities\User;

class TokenRepository extends EntityRepository
{

    /**
     * @throws \Exception
     */
    public function clearUsersOldTokens(Token $currentToken)
    {
        $this->gc();
        $this->clearInactiveTokensByUser($currentToken->getUser());
        $this->clearDuplicateTokens($currentToken);
        $this->clearTokenIfMaxCount($currentToken);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function clearTokenIfMaxCount(Token $currentToken)
    {
        $maxCount = Config::get('security', 'max_tokens', 5);
        $allTokensCount = $this->count(['user' => $currentToken->getUser()]);

        if($allTokensCount <= $maxCount){
            return;
        }

        $tokens = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.token != :current_token')
            ->setParameter('current_token', $currentToken->getToken())
            ->andWhere('t.user = :user')
            ->setParameter('user', $currentToken->getUser())
            ->orderBy('t.lastUsed', 'desc')
            ->setMaxResults($allTokensCount-$maxCount)
            ->getQuery()
            ->getResult()
        ;

        foreach ($tokens as $token) {
            $this->getEntityManager()->remove($token);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function clearDuplicateTokens(Token $currentToken)
    {
        $tokens = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.fingerprint = :fingerprint')
            ->setParameter('fingerprint', $currentToken->getFingerprint())
            ->andWhere('t.token != :current_token')
            ->setParameter('current_token', $currentToken->getToken())
            ->getQuery()
            ->getResult()
        ;
        foreach ($tokens as $token) {
            $this->getEntityManager()->remove($token);
        }
        $this->getEntityManager()->flush();
    }

    public function clearInactiveTokensByUser(User $user)
    {
        return $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.exp < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
            ;
    }

    public function clearAllInactiveTokens()
    {
        return $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->andWhere('t.exp < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * gcProbability int 10 the probability (parts per million) that garbage collection (GC) should be performed
     *      when storing a piece of data in the cache. Defaults to 10, meaning 0.001% chance.
     *      This number should be between 0 and 1000000. A value 0 means no GC will be performed at all.
     */
    private int $gcProbability = 10;

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