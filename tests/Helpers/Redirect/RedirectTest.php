<?php

declare(strict_types=1);

namespace Tests\EnjoysCMS\Helpers\Redirect;

use EnjoysCMS\Core\Helpers\Redirect\Redirect;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class RedirectTest extends TestCase
{

    public function testRedirectResponse()
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(
            new class () {
                public function __toString(): string
                {
                    return '/url';
                }
            }
        );
        $response = new Response();
        $redirect = new Redirect($request, $response);
        $result = $redirect->http('/redirect');
        $this->assertSame(['/redirect'], $result->getHeader('Location'));
        $this->assertSame(302, $result->getStatusCode());

        $result = $redirect->http(code: 301);
        $this->assertSame(['/url'], $result->getHeader('Location'));
        $this->assertSame(301, $result->getStatusCode());
    }
}
