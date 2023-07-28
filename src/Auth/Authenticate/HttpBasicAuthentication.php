<?php

namespace EnjoysCMS\Core\Auth\Authenticate;

use EnjoysCMS\Core\Auth\Authentication;
use EnjoysCMS\Core\Users\Entity\User;
use Psr\Http\Message\ServerRequestInterface;

class HttpBasicAuthentication implements Authentication
{

    public function __construct(private readonly LoginPasswordAuthentication $loginPasswordAuthentication)
    {
    }

    public function authenticate(ServerRequestInterface $request): ?User
    {
        $token = $this->getTokenFromHeaders($request);
        if ($this->isBasicToken($token)){
            [$username, $password] = $this->getCredentials($token);
            return $this->loginPasswordAuthentication->authenticate($request->withQueryParams([
                'login' => $username,
                'password' => $password
            ]));
        }
        return null;
    }

    private function getTokenFromHeaders(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (!empty($header)) {
            return $header;
        }

        return $request->getServerParams()['REDIRECT_HTTP_AUTHORIZATION'] ?? null;
    }

    private function isBasicToken(?string $token): bool
    {
        if ($token === null){
            return false;
        }
        return str_starts_with(strtolower($token), 'basic');
    }

    private function getCredentials(string $token): array
    {
        return array_map(
            static fn ($value) => $value === '' ? null : $value,
            explode(':', base64_decode(substr($token, 6)), 2)
        );
    }
}
