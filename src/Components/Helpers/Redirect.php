<?php


namespace EnjoysCMS\Core\Components\Helpers;


use HttpSoft\Emitter\SapiEmitter;
use HttpSoft\Message\Response;
use HttpSoft\ServerRequest\ServerRequestCreator;

class Redirect
{
    public static function http($uri = null, $code = 302)
    {
        $response = new Response(
            $code, [
            'Location' => $uri ?? self::getCurrentUri()->__toString()
        ]
        );

        $emitter = new SapiEmitter();
        $emitter->emit($response);
        exit;
    }

    public static function html($url, $delay = 0)
    {
        echo "<META HTTP-EQUIV='REFRESH' CONTENT='{$delay};URL={$url}'>";
    }

    private static function getCurrentUri()
    {
        $request = ServerRequestCreator::create();
        return $request->getUri();
    }


}
