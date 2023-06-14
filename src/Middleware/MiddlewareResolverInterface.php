<?php

namespace EnjoysCMS\Core\Middleware;

interface MiddlewareResolverInterface
{

    public function resolve(mixed $entry);
}
