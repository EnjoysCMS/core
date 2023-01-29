<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Breadcrumbs;

final class Breadcrumbs implements BreadcrumbsInterface
{
    private array $bc = [];

    public function add(?string $url = null, ?string $title = null)
    {
        $breadcrumb = new \stdClass();
        $breadcrumb->url = $url;
        $breadcrumb->title = $title;

        $this->bc[] = $breadcrumb;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->bc;
    }
}
