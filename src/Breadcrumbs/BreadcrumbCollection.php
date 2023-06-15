<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Breadcrumbs;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;

final class BreadcrumbCollection
{

    public function __construct(private ?UrlGeneratorInterface $urlGenerator = null)
    {
    }

    /**
     * @var BreadcrumbInterface[]
     */
    private array $stack = [];

    public function addRoute(string|array|Route $route, ?string $title = null): BreadcrumbCollection
    {
        if ($this->urlGenerator === null) {
            throw new \RuntimeException(
                sprintf(
                    'The BreadcrumbCollection::addRoute() method cannot be used because "%s" is not specified.
                Call BreadcrumbCollection::setUrlGenerator($urlGenerator) ',
                    UrlGeneratorInterface::class
                )
            );
        }

        $routeBreadcrumbResolver = new RouteBreadcrumbResolver($this->urlGenerator);
        $this->add($routeBreadcrumbResolver->getBreadcrumb($route, $title));
        return $this;
    }

    public function addUrl(?string $url = null, ?string $title = null): BreadcrumbCollection
    {
        $this->add(new Breadcrumb($url, $title));
        return $this;
    }


    public function addBreadcrumbWithoutUrl(string $title): BreadcrumbCollection
    {
        $this->add(new Breadcrumb(title: $title));
        return $this;
    }

    private function add(BreadcrumbInterface $breadcrumb): void
    {
        $this->stack[] = $breadcrumb;
    }



    /**
     * @return BreadcrumbInterface[]
     */
    public function get(): array
    {
        return $this->stack;
    }

    public function getKeyValueArray(): array
    {
        $result = [];
        /** @var Breadcrumb $item */
        foreach ($this->stack as $item) {
            $result[$item->url] = $item->title;
        }
        return $result;
    }

    /**
     * @param UrlGeneratorInterface|null $urlGenerator
     */
    public function setUrlGenerator(?UrlGeneratorInterface $urlGenerator): void
    {
        $this->urlGenerator = $urlGenerator;
    }



}
