<?php

namespace EnjoysCMS\Core\Breadcrumbs;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RouteBreadcrumbResolver
{

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @param array{string, array}|string $data
     * @param string|null $title
     * @return BreadcrumbInterface
     */
    public function getBreadcrumb(array|string $data, ?string $title): BreadcrumbInterface
    {
        $url = $this->getUrl($data);
        return new Breadcrumb($url, $title);
    }


    /**
     * @param array{string, array}|string $data
     * @return string
     */
    private function getUrl(array|string $data): string
    {
        [$routeName, $routeParams] = $this->resolveRoute($data);

        return $this->urlGenerator->generate($routeName, $routeParams);
    }

    /**
     * @param array{string, array}|string $data
     * @return array{string, array}
     */
    private function resolveRoute(array|string $data): array
    {
        if (is_string($data)){
            return [$data, []];
        }

        return [$data[0], $data[1] ?? []];

    }
}
