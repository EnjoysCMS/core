<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Breadcrumbs;

interface BreadcrumbsInterface
{
    public function add(string $url = null, string $title = null);

    public function get(): array;
}
