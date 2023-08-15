<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Modules;


use Enjoys\Config\Config;

abstract class AbstractModuleConfig
{

    abstract public function getConfig(): Config;

    abstract public function getModulePackageName(): string;

    final public function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->getConfig()->get($this->getModulePackageName());
        }
        return $this->getConfig()->get(sprintf('%s->%s', $this->getModulePackageName(), $key), $default);
    }


    final public function all(): array
    {
        return $this->get();
    }
}
