<?php

namespace EnjoysCMS\Core\Breadcrumbs;

class Breadcrumb implements BreadcrumbInterface, \Stringable
{

    public function __construct(
        public readonly ?string $url = null,
        public readonly ?string $title = null
    ) {
    }

    public function __toString(): string
    {
        return $this->title ?? '';
    }
}
