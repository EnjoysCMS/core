<?php

namespace Tests\EnjoysCMS\Http\Response;

use EnjoysCMS\Core\Http\Emitter\EmitterInterface;
use EnjoysCMS\Core\Http\Response\Redirect;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tests\EnjoysCMS\Traits\MockHelper;

class RedirectTest extends TestCase
{

    use MockHelper;

    private ServerRequestInterface $request;
    private EmitterInterface $emitter;
    private UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->request = $this->getMock(ServerRequestInterface::class);
        $this->emitter = $this->getMock(EmitterInterface::class);
        $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);
    }

    public function testToRoute()
    {
        $this->request->method('getUri')->willReturn(
            new class () {
                public function __toString(): string
                {
                    return '/url';
                }
            }
        );

        $this->urlGenerator->method('generate')->willReturn('/redirect');

        $redirect = new Redirect($this->request, new Response(), $this->emitter, $this->urlGenerator, function () {
            echo 'emitted';
        });

        $result = $redirect->toRoute('route');
        $this->assertSame(['/redirect'], $result->getHeader('Location'));
        $this->assertSame(302, $result->getStatusCode());

        $result = $redirect->toRoute('route', code: 301);
        $this->assertSame(['/redirect'], $result->getHeader('Location'));
        $this->assertSame(301, $result->getStatusCode());

        ob_start();
        $redirect->toUrl('route', emit: true);
        $result = ob_get_clean();
        $this->assertSame('emitted', $result);
    }

    public function testToUrl()
    {
        $this->request->method('getUri')->willReturn(
            new class () {
                public function __toString(): string
                {
                    return '/url';
                }
            }
        );
        $redirect = new Redirect($this->request, new Response(), $this->emitter, $this->urlGenerator, function () {
            echo 'emitted';
        });

        $result = $redirect->toUrl('/redirect');
        $this->assertSame(['/redirect'], $result->getHeader('Location'));
        $this->assertSame(302, $result->getStatusCode());

        $result = $redirect->toUrl(code: 301);
        $this->assertSame(['/url'], $result->getHeader('Location'));
        $this->assertSame(301, $result->getStatusCode());

        ob_start();
        $redirect->toUrl(emit: true);
        $result = ob_get_clean();
        $this->assertSame('emitted', $result);
    }
}
