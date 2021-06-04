<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Detector;


final class Browser
{
    public static function getFingerprint()
    {
        $data = $_SERVER['HTTP_USER_AGENT'];
        return hash_hmac('sha256', $data, 'ddd');
    }

}