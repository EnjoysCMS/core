<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Helpers\Redirect;

use EnjoysCMS\Core\Helpers\Redirect\Redirect;
use EnjoysCMS\Core\Interfaces\EmitterInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RedirectTest
{

    public function testRedirectResponse()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $emitter = $this->createMock(EmitterInterface::class);
        $request->method('getUri')->willReturn(
            new class () {
                public function __toString(): string
                {
                    return '/url';
                }
            }
        );

        $redirect = new Redirect(
            request: $request,
            response: new Response(),
            emitter: $emitter,
            terminateClosure: function () {
                echo 'emitted';
            }
        );
        $result = $redirect->http('/redirect');
        $this->assertSame(['/redirect'], $result->getHeader('Location'));
        $this->assertSame(302, $result->getStatusCode());

        $result = $redirect->http(code: 301);
        $this->assertSame(['/url'], $result->getHeader('Location'));
        $this->assertSame(301, $result->getStatusCode());

        ob_start();
        $redirect->http(emit: true);
        $result = ob_get_clean();
        $this->assertSame('emitted', $result);
    }
}
