<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Middleware;

use ArrayIterator;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class HttpMiddlewareDispatcher implements RequestHandlerInterface
{

    private ArrayIterator $queue;

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
        if (is_array($queue)) {
            $queue = new ArrayIterator($queue);
        }

        if ($queue->count() === 0) {
            throw new InvalidArgumentException('$queue cannot be empty');
        }

        /** @var ArrayIterator $queue */
        $this->queue = $queue;
        $this->queue->seek(0);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->queue->valid()) {
            return $this->requestHandler->handle($request);
        }

        $entry = $this->queue->current();

        $middleware = $this->resolver?->resolve($entry) ?? $entry;

        $this->queue->next();

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

    public function addQueue(array $routeMiddlewares): void
    {
        $key = $this->queue->key();
        $queue = iterator_to_array($this->queue);
        // array_reverse нужен для того, чтобы вставить массив как есть, так как
        // $key всё время одинаковый, иначе он вставится перевернутым
        foreach (array_reverse($routeMiddlewares) as $routeMiddleware) {
            $queue = array_insert_before($queue, $key, $routeMiddleware);
        }
        $this->queue = new ArrayIterator($queue);
        $this->queue->seek($key);
        dd($this->queue);
    }

}
