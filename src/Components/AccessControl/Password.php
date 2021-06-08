<?php


namespace EnjoysCMS\Core\Components\AccessControl;


class Password
{
    final public static function getHash(string $password, string|int|null $algo = \PASSWORD_DEFAULT): string
    {
        return password_hash($password, $algo);
    }

    final public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }


    private const PASSW_LENGTH = 6;
    private const PASSW_CHARS = [];


    /**
     * @param  int   $length
     * @param  array $chars
     * @return string
     * @todo
     */
    final public static function generate(int $length = self::PASSW_LENGTH, array $chars = self::PASSW_CHARS): string
    {
        return '';
    }
}
