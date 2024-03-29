<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Interfaces;


use Psr\Http\Message\ResponseInterface;

/**
 * @see https://github.com/httpsoft/http-emitter/blob/master/src/EmitterInterface.php
 */
interface EmitterInterface
{
    /**
     * Emits a HTTP response, that including status line, headers and message body, according to the environment.
     *
     * When implementing this method, MAY use `header()` and the output buffer. Also implementations MAY throw
     * exceptions if cannot emit a response, e.g., if headers already sent or output has been emitted previously.
     *
     * @param ResponseInterface $response
     * @param bool $withoutBody
     */
    public function emit(ResponseInterface $response, bool $withoutBody = false): void;
}
