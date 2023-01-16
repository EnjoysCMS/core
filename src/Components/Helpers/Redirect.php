<?php

namespace EnjoysCMS\Core\Components\Helpers;

use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Message\Response;
use HttpSoft\ServerRequest\ServerRequestCreator;
use Psr\Http\Message\UriInterface;

class Redirect
{
    public static function http($uri = null, $code = 302): void
    {
        $response = new Response($code, [
            'Location' => $uri ?? self::getCurrentUri()->__toString()
        ]);

        $emitter = new SapiEmitter();
        $emitter->emit($response);
        exit;
    }

    public static function html($url, $delay = 0): void
    {
        echo sprintf("<META HTTP-EQUIV='REFRESH' CONTENT='%s;URL=%s'>", $delay, $url);
    }

    private static function getCurrentUri(): UriInterface
    {
        $request = ServerRequestCreator::create();
        return $request->getUri();
    }
}
