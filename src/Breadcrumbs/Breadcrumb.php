<?php

namespace EnjoysCMS\Core\Breadcrumbs;

use Stringable;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Breadcrumb implements BreadcrumbInterface, Stringable
{
    private ?string $title = null;
    private ?string $url = null;

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }


    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param array{string, array}|string|null $data
     * @return void
     */
    public function setUrl(string|array|null $data): void
    {
        $this->url = $this->resolveUrl($data);
    }

    /**
     * @param array{string, array}|string|null $data
     * @return string
     */
    private function resolveUrl(array|string|null $data): string
    {
        if ($data === null) {
            return '';
        }

        [$routeName, $routeParams] = $this->resolveRoute($data);

        try {
            return $this->urlGenerator->generate($routeName, $routeParams);
        } catch (RouteNotFoundException) {
            return $routeName;
        }
    }

    /**
     * @param array{string, array}|string $data
     * @return array{string, array}
     */
    private function resolveRoute(array|string $data): array
    {
        if (is_string($data)) {
            return [$data, []];
        }

        return [$data[0], $data[1] ?? []];
    }
}
