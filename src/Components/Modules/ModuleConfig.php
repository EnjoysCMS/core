<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Modules;


use Enjoys\Config\Config;
use Psr\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

final class ModuleConfig
{

    private ?array $config;

    private Config $containerConfig;

    private ?Module $module;

    public function __construct(string $moduleName, ContainerInterface $container)
    {
        $this->module = $container->get(ModuleCollection::class)->find('packageName', $moduleName);

        if ($this->module === null) {
            throw new \InvalidArgumentException(
                sprintf('Module %s not found. Name must be same like packageName in module composer.json', $moduleName)
            );
        }

        $this->containerConfig = $container->get('Config');

        $this->initConfig();
    }


    private function initConfig(bool $break = false): void
    {
        $this->config = $this->containerConfig->getConfig($this->module->packageName);

        if ($break === true) {
            return;
        }
        if ($this->config === null) {
            $this->containerConfig->addConfig(
                $this->module->path . '/config.yml',
                ['flags' => Yaml::PARSE_CONSTANT],
                Config::YAML
            );

            $this->initConfig(true);
        }
    }

    public function get(string $key): string
    {
        if (array_key_exists($key, (array)$this->config)) {
            return $this->config[$key];
        }

        throw new \InvalidArgumentException(
            sprintf('Config param [%s => %s] not found', $this->module->packageName, $key)
        );
    }

    public function getAll(): array
    {
        return (array)$this->config;
    }
}