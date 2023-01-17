<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\Modules;

use Enjoys\Config\Config;
use Symfony\Component\Yaml\Yaml;

final class ModuleConfig
{
    private bool $strict = true;

    private ?array $config;

    private ?Module $module;

    /**
     * @param string $moduleName
     * @param Config $config
     * @param ModuleCollection $moduleCollection
     * @throws \Exception
     */
    public function __construct(
        string $moduleName,
        Config $config,
        ModuleCollection $moduleCollection
    ) {
        $this->module = $moduleCollection->find($moduleName);

        if ($this->module === null) {
            throw new \InvalidArgumentException(
                sprintf('Module %s not found. Name must be same like packageName in module composer.json', $moduleName)
            );
        }

        $this->init($config);
    }


    /**
     * @throws \Exception
     */
    private function init(Config $config): void
    {
        if (file_exists($this->module->path . '/config.yml')) {
            $config->addConfig(
                [
                    $this->module->packageName => file_get_contents($this->module->path . '/config.yml')
                ],
                ['flags' => Yaml::PARSE_CONSTANT],
                Config::YAML,
                false
            );
        }

        $this->config = $config->getConfig($this->module->packageName);
    }

    public function strict(bool $strict): ModuleConfig
    {
        $this->strict = $strict;
        return $this;
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        if (array_key_exists($key, (array)$this->config)) {
            return $this->config[$key];
        }

        if ($this->strict) {
            throw new \InvalidArgumentException(
                sprintf('Config param [%s => %s] not found', $this->module->packageName, $key)
            );
        }

        return $default;
    }

    /**
     * @deprecated since 4.3.7. use asArray(). remove in 5.0
     */
    public function getAll(): array
    {
        return $this->asArray();
    }

    public function asArray(): array
    {
        return (array)$this->config;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }
}
