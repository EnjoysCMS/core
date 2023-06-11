<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Users\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Users\Repository\TokenRepository;

#[ORM\Entity(repositoryClass: TokenRepository::class)]
#[ORM\Table(name: 'tokens')]
class Token
{

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private string $token;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $exp;

    #[ORM\Column(type: 'string', length: 255,nullable: true)]
    private ?string $fingerprint = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $lastUsed;


    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }


    public function getExp(): DateTimeImmutable
    {
        return $this->exp;
    }

    public function setExp(DateTimeImmutable $exp): void
    {
        $this->exp = $exp;
    }


    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }


    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getLastUsed(): DateTimeImmutable
    {
        return $this->lastUsed;
    }

    public function setLastUsed(DateTimeImmutable $lastUsed): void
    {
        $this->lastUsed = $lastUsed;
    }
}
