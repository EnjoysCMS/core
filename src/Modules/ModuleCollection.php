<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Modules;

use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;

final class ModuleCollection implements IteratorAggregate
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
                throw new InvalidArgumentException(sprintf('Property %s is invalid', $property));
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
    public function all(): array
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

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }
}
