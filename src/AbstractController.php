<?php

declare(strict_types=1);


namespace EnjoysCMS\Core;


use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use EnjoysCMS\Core\Breadcrumbs\BreadcrumbCollection;
use EnjoysCMS\Core\Http\Response\RedirectInterface;
use EnjoysCMS\Core\Setting\Setting;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

abstract class AbstractController
{

    protected ResponseInterface $response;
    protected ServerRequestInterface $request;
    protected RedirectInterface $redirect;
    protected Environment $twig;
    protected Setting $setting;
    protected BreadcrumbCollection $breadcrumbs;
    protected EventDispatcherInterface $dispatcher;

    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function __construct(protected Container $container)
    {
        $this->response = $this->container->get(ResponseInterface::class);
        $this->request = $this->container->get(ServerRequestInterface::class);
        $this->redirect = $this->container->get(RedirectInterface::class);
        $this->twig = $this->container->get(Environment::class);
        $this->setting = $this->container->get(Setting::class);
        $this->breadcrumbs = $this->container->get(BreadcrumbCollection::class);
        $this->dispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    /**
     * @see textResponse
     */
    final protected function response(string $body, int $statusCode = 200): ResponseInterface
    {
        return $this->textResponse($body, $statusCode);
    }

    final protected function textResponse(string $body, int $statusCode = 200): ResponseInterface
    {
        $this->response = $this->response->withStatus($statusCode);
        $this->writeBody($body);
        return $this->response;
    }

    /**
     * @see jsonResponse
     */
    final protected function json(mixed $payload, int $statusCode = 200): ResponseInterface
    {
        return $this->jsonResponse($payload, $statusCode);
    }

    final protected function jsonResponse(mixed $payload, int $statusCode = 200): ResponseInterface
    {
        $this->response = $this->response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json');

        $this->writeBody(json_encode($payload));
        return $this->response;
    }

    private function writeBody(string $body): void
    {
        $this->response->getBody()->write($body);
    }
}
