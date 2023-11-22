<?php

namespace EnjoysCMS\Core\Modules;

use stdClass;

class Module
{
    public string $installPath;
    public string $path;
    public string $packageName;
    public ?string $description = null;
    public array $paths;
    public string $moduleName;
    public array $adminLinks = [];
    public array $namespaces = [];
    public ?stdClass $extra = null;

    public function __construct(object $data)
    {
        $this->installPath = $this->path = $data->installPath;
        $this->packageName = $data->packageName;
        $this->extra = $data->extra;
        $this->paths = $data->paths;
        $this->moduleName = $this->extra?->{'module-name'} ?? $this->packageName;
        $this->namespaces = $data->namespaces;
        $this->adminLinks = (array)($this->extra?->{'admin-links'} ?? null);
    }
}
