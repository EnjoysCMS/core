<?php

namespace EnjoysCMS\Core\Breadcrumbs;

interface BreadcrumbInterface implements \Stringable
{
    public function getUrl(): ?string;

    public function getTitle(): ?string;
}
