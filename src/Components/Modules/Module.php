<?php


namespace EnjoysCMS\Core\Components\Modules;


use EnjoysCMS\Core\Components\Composer\Utils;

class Module
{
    public string $installPath;
    public ?string $routePath;
    public string $packageName;
    public string $description;
    public array $paths;
    public bool $useMigrations = false;
    public string $moduleName;
    public array $adminLinks = [];

    public function __construct(object $data)
    {
        foreach ($data as $k => $v){
            $this->$k = $v;
        }
        $this->moduleName = $this->packageName;
        $this->routePath = $this->getRoutePath();

        if(isset($this->extra->{'use-migrations'})) {
            $this->useMigrations =  (bool)$this->extra->{'use-migrations'};
        }

        if(isset($this->extra->{'module-name'})) {
            $this->moduleName =  $this->extra->{'module-name'};
        }
        if(isset($this->extra->{'admin-links'})) {
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
