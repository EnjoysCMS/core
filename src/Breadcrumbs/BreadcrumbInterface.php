<?php

namespace EnjoysCMS\Core\Breadcrumbs;

interface BreadcrumbInterface
{
    public function getUrl(): ?string;

    public function getTitle(): ?string;
}
