<?php

namespace EnjoysCMS\Core\Breadcrumbs;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BreadcrumbUrlResolver implements BreadcrumbUrlResolverInterface
{

    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
    }

    /**
     * @param array{string, ?array} $dataUrl
     * @return string
     */
    public function resolve(array $dataUrl): string
    {
        [$routeName, $routeParams] = $this->resolveRouteParams($dataUrl);

        try {
            return $this->urlGenerator->generate($routeName, $routeParams);
        } catch (RouteNotFoundException) {
            $this->logger->debug(
                sprintf('[%s] The route name "%s" not found. Url returned as is.', __CLASS__, $routeName)
            );
            return $routeName;
        }
    }

    /**
     * @param array{string, ?array} $data
     * @return array{string, array}
     */
    private function resolveRouteParams(array $data): array
    {
        return [$data[0], $data[1] ?? []];
    }
}
