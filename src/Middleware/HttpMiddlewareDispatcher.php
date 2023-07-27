<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class HttpMiddlewareDispatcher implements RequestHandlerInterface
{

    /**
     * @var array<int, mixed>
     */
    private array $queue;

    /**
     * @param RequestHandlerInterface $requestHandler
     * @param MiddlewareResolverInterface|null $resolver
     */
    public function __construct(
        private readonly RequestHandlerInterface $requestHandler,
        private readonly ?MiddlewareResolverInterface $resolver = null
    ) {
    }

    public function setQueue(iterable $queue): void
    {
        if (!is_array($queue)) {
            $queue = iterator_to_array($queue);
        }

        if (empty($queue)) {
            throw new InvalidArgumentException('$queue cannot be empty');
        }

        /** @var array<int, mixed> $queue */
        $this->queue = $queue;
        reset( $this->queue);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $entry = current($this->queue);

        if ($entry === false) {
            return $this->requestHandler->handle($request);
        }

        $middleware = $this->resolver?->resolve($entry) ?? $entry;

        next($this->queue);


        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }

        if (is_callable($middleware)) {
            /** @var callable(ServerRequestInterface, RequestHandlerInterface):ResponseInterface $middleware */
            return $middleware($request, $this);
        }

        throw new RuntimeException(
            sprintf(
                'Invalid middleware queue entry: %s. Middleware must either be callable or implement %s.',
                get_debug_type($middleware),
                MiddlewareInterface::class
            )
        );
    }

}
