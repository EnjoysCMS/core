<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Entities;

use Doctrine\ORM\Mapping as ORM;
use EnjoysCMS\Core\Components\Helpers\Config;

/**
 * Class Token
 * @package EnjoysCMS\Core\Entities
 * @ORM\Entity(repositoryClass="EnjoysCMS\Core\Repositories\TokenRepository")
 * @ORM\Table(name="tokens")
 */
class Token
{

    public static function getTokenName()
    {
        return Config::get('security', 'token_name', '_token_refresh');
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private string $token;


    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $exp;


    public function getExp(): \DateTimeImmutable
    {
        return $this->exp;
    }

    public function setExp(\DateTimeImmutable $exp): void
    {
        $this->exp = $exp;
    }

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $fingerprint = null;


    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     */
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(Users $user)
    {
        $this->user = $user;
    }

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $lastUsed;


    public function getLastUsed(): \DateTimeImmutable
    {
        return $this->lastUsed;
    }

    public function setLastUsed(\DateTimeImmutable $lastUsed): void
    {
        $this->lastUsed = $lastUsed;
    }
}