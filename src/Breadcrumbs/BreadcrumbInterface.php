<?php

namespace EnjoysCMS\Core\Breadcrumbs;

interface BreadcrumbInterface extends \Stringable
{
    public function getUrl(): ?string;

    public function getTitle(): ?string;
}
