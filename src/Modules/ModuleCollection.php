<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Modules;

final class ModuleCollection
{
    /**
     * @var Module[]
     */
    private array $collection = [];


    public function find(string $value): ?Module
    {
        return $this->findBy('packageName', $value);
    }

    public function findBy(string $property, string $value): ?Module
    {
        foreach ($this->collection as $module) {
            if (!property_exists($module, $property)) {
                throw new \InvalidArgumentException(sprintf('Property %s is invalid', $property));
            }

            if ($module->{$property} === $value) {
                return $module;
            }
        }
        return null;
    }

    /**
     * @return Module[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }


    public function addModule(Module $module): void
    {
        if ($this->has($module)){
            return;
        }
        $this->collection[] = $module;
    }

    public function has(Module $module): bool
    {
        foreach ($this->collection as $item) {
            if ($item->moduleName === $module->moduleName){
                return true;
            }
        }
        return false;
    }
}
