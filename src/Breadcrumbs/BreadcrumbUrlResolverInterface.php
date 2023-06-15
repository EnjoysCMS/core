<?php

namespace EnjoysCMS\Core\Breadcrumbs;

interface BreadcrumbUrlResolverInterface
{
    /**
     * @param array{string, ?array} $dataUrl
     * @return string
     */
    public function resolve(array $dataUrl): string;
}
