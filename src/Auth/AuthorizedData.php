<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Auth;

final class AuthorizedData
{
    public $data = null;

    public function __construct(public readonly ?int $userId)
    {
    }

    public static function fromArray(array $data): AuthorizedData
    {
        $authData = new self($data['user']['id'] ?? null);
        $authData->data = $data;
        return $authData;
    }
}
