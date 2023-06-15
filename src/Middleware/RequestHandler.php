<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Middleware;


use DI\Container;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Symfony\Component\Routing\Route;

class RequestHandler implements RequestHandlerInterface
{

    /**
     * @var string Attribute name for handler reference
     */
    private string $handlerAttribute = '_controller';


    public function __construct(private readonly Container $container)
    {

    }


    public function handlerAttribute(string $handlerAttribute): self
    {
        $this->handlerAttribute = $handlerAttribute;
        return $this;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Route $route */
        $route = $request->getAttribute('_route');
        $requestHandler = $route->getDefault($this->handlerAttribute);
        if (empty($requestHandler)) {
            throw new RuntimeException(
                sprintf('Empty request handler %s', $this->handlerAttribute)
            );
        }

        $this->container->set(ServerRequestInterface::class, $request);

        if (is_string($requestHandler)) {
            $requestHandler = $this->container->get($requestHandler);
        }

        if (is_array($requestHandler)
            && count($requestHandler) === 2
            && is_string($requestHandler[0])
        ) {
            $requestHandler[0] = $this->container->get($requestHandler[0]);
        }


        return $this->container->call($requestHandler);
    }
}
