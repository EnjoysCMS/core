<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Detector;


final class Browser
{
    public static function getFingerprint(): string
    {
        $data = '';
        $data .= $_SERVER['HTTP_USER_AGENT'] ?? 'http_user_agent';
        $data .= $_SERVER['HTTP_ACCEPT'] ?? 'http_accept';
        $data .= $_SERVER['HTTP_ACCEPT_ENCODING'] ?? 'http_accept_encoding';
        $data .= $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'http_accept_language';

        return hash_hmac('sha256', $data, $_ENV['SECRET_PHRASE'] ?? 'secret phrase');
    }

}