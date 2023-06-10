<?php

namespace EnjoysCMS\Core\Modules;

class Module
{
    public string $installPath;
    public string $path;
    public ?string $routePath;
    public string $packageName;
    public string $description;
    public array $paths;
    public string $moduleName;
    public array $adminLinks = [];
    public array $namespaces = [];

    public function __construct(object $data)
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
        $this->path = $this->installPath;
        $this->moduleName = $this->packageName;
        $this->routePath = $this->getRoutePath();

        if (isset($this->extra->{'module-name'})) {
            $this->moduleName =  $this->extra->{'module-name'};
        }
        if (isset($this->extra->{'admin-links'})) {
            $this->adminLinks =  (array)$this->extra->{'admin-links'};
        }
    }



    private function getRoutePath(): ?string
    {
        $routePath = $this->installPath . '/routes.yml';
        if (file_exists($routePath)) {
            return $routePath;
        }
        if (file_exists($routePath . '.dist')) {
            return $routePath . '.dist';
        }

        return null;
    }
}
