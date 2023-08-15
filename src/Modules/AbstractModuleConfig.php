<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Modules;


use Enjoys\Config\Config;

abstract class AbstractModuleConfig
{

    public function __construct(private readonly Config $config)
    {
    }

    abstract public function getModulePackageName(): string;

    final public function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config->get($this->getModulePackageName());
        }
        return $this->config->get(sprintf('%s->%s', $this->getModulePackageName(), $key), $default);
    }


    final public function all(): array
    {
        return $this->get();
    }
}
