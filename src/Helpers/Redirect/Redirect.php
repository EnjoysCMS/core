<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Helpers\Redirect;

use EnjoysCMS\Core\Interfaces\EmitterInterface;
use EnjoysCMS\Core\Interfaces\RedirectInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class Redirect implements RedirectInterface
{

    public function __construct(
        private ServerRequestInterface $request,
        private ResponseInterface $response,
        private EmitterInterface $emitter,
        private ?\Closure $terminateClosure = null,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function http(string $uri = null, int $code = 302, bool $emit = false): ResponseInterface
    {
        $response = $this->response
            ->withStatus($code)
            ->withHeader(
                'Location',
                $uri ?? $this->request->getUri()->__toString()
            )
        ;

        if ($emit === true) {
            $this->emitter->emit($response);
            if ($this->terminateClosure === null) {
                exit(0);
            }
            ($this->terminateClosure)();
        }
        return $response;
    }


}
