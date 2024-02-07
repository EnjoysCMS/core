<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Users\Repository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Enjoys\Config\Config;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Entity\User;
use Exception;

use function random_int;

/**
 * @method Token|null find($id, $lockMode = null, $lockVersion = null)
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 * @method list<Token> findAll()
 * @method list<Token> findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenRepository extends EntityRepository
{

    /**
     * @throws Exception
     */
    public function clearUsersOldTokens(Token $currentToken, Config $config): void
    {
        $this->gc();
        $this->clearInactiveTokensByUser($currentToken->getUser());
        $this->clearTokenIfMaxCount($currentToken, $config);
    }

    public function clearTokenIfMaxCount(Token $currentToken,  Config $config): void
    {
        $maxCount = $config->get('security->max_tokens', 0);

        if ($maxCount <= 0) {
            return;
        }

        $allTokensCount = $this->count(['user' => $currentToken->getUser()]);

        if ($allTokensCount <= $maxCount) {
            return;
        }

        $tokens = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.token != :current_token')
            ->setParameter('current_token', $currentToken->getToken())
            ->andWhere('t.user = :user')
            ->setParameter('user', $currentToken->getUser())
            ->orderBy('t.lastUsed', 'desc')
            ->setMaxResults($allTokensCount - $maxCount)
            ->getQuery()
            ->getResult()
        ;

        foreach ($tokens as $token) {
            $this->getEntityManager()->remove($token);
        }
    }

    public function clearInactiveTokensByUser(User $user)
    {
        return $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->where('t.user = :user')
            ->setParameter('user', $user)
            ->andWhere('t.exp < :now')
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getResult()
            ;
    }

    public function clearAllInactiveTokens()
    {
        return $this->createQueryBuilder('t')
            ->delete(Token::class, 't')
            ->andWhere('t.exp < :now')
            ->setParameter('now', new DateTimeImmutable())
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * gcProbability int 10 the probability (parts per million) that garbage collection (GC) should be performed
     * when storing a piece of data in the cache. Defaults to 10, meaning 0.001% chance.
     * This number should be between 0 and 1000000. A value 0 means no GC will be performed at all.
     */
    private int $gcProbability = 10;

    /**
     * @throws Exception
     */
    private function gc(): void
    {
        if (random_int(0, 1000000) < $this->gcProbability) {
            $this->clearAllInactiveTokens();
        }
    }
}
