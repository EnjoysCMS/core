<?php

namespace EnjoysCMS\Core\Auth\TokenStorage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Auth\TokenStorageInterface;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Repository\TokenRepository;

class DatabaseTokenStorage implements TokenStorageInterface
{

    private TokenRepository|EntityRepository $tokenRepository;

    public function __construct(private readonly EntityManagerInterface $em)
    {
        $this->tokenRepository = $this->em->getRepository(Token::class);
    }

    public function find(string $token)
    {
        return $this->tokenRepository->find($token);
    }
}
