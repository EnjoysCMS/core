<?php

namespace EnjoysCMS\Core\Auth\TokenStorage;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use EnjoysCMS\Core\Auth\TokenStorageInterface;
use EnjoysCMS\Core\Users\Entity\Token;
use EnjoysCMS\Core\Users\Repository\TokenRepository;

class DatabaseTokenStorage implements TokenStorageInterface
{

    public function __construct(private readonly TokenRepository $tokenRepository)
    {
    }

    public function find(string $token): ?Token
    {
        return $this->tokenRepository->find($token);
    }
}
