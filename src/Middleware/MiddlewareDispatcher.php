<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class MiddlewareDispatcher implements RequestHandlerInterface
{

    /**
     * @var array<int, mixed>
     */
    private array $queue;

    /**
     * @param array<int, mixed>|iterable $queue
     * @param RequestHandlerInterface $requestHandler
     * @param MiddlewareResolverInterface|null $resolver
     */
    public function __construct(
        iterable $queue,
        private readonly RequestHandlerInterface $requestHandler,
        private readonly ?MiddlewareResolverInterface $resolver = null
    ) {
        if (!is_array($queue)) {
            $queue = iterator_to_array($queue);
        }

        if (empty($queue)) {
            throw new InvalidArgumentException('$queue cannot be empty');
        }

        /** @var array<int, mixed> $queue */
        $this->queue = $queue;
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

    public function addToQueue(mixed $middleware): void
    {
        $key = key($this->queue);
        $this->queue = array_insert_before($this->queue, $key, $middleware);
        $this->setInternalPoint($key);
    }

    private function setInternalPoint(int $point): void
    {
        while (key($this->queue) !== null) {
            if ($point === key($this->queue)) {
                return;
            }
            next($this->queue);
        }
    }
}
