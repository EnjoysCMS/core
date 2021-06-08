<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Components\Modules;


use Psr\Container\ContainerInterface;

final class ModuleCollection
{

    private array $collection;

    public function __construct(ContainerInterface $container)
    {
        $this->collection = $container->get('Modules');
    }

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
}