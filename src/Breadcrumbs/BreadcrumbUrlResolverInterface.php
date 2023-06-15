<?php

namespace EnjoysCMS\Core\Breadcrumbs;

interface BreadcrumbUrlResolverInterface
{
    /**
     * @param array{string, ?array} $data
     * @return string
     */
    public function resolve(array $data): string;
}
