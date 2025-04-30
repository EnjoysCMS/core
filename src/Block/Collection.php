<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;


use ArrayAccess;
use ArrayIterator;
use Countable;
use EnjoysCMS\Core\Block\Annotation\Attributes;
use IteratorAggregate;
use ReflectionClass;

/**
 * @implements  ArrayAccess<array-key, Attributes>
 * @implements  IteratorAggregate<array-key, Attributes>
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{

    /**
     * @var Attributes[]
     */
    private array $collection = [];

    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @return Attributes[]
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function addCollection(Collection $collection): void
    {
        /** @var Attributes $annotation */
        foreach ($collection as $annotation) {
            $this->addAnnotation($annotation);
        }
    }

    public function addAnnotation(Attributes $annotation): void
    {
        $this->collection[] = $annotation;
    }

    public function getAnnotation(ReflectionClass $class): ?Attributes
    {
        foreach ($this->collection as $annotation) {
            if ($annotation->getClassName() === $class->getName()) {
                return $annotation;
            }
        }

        return null;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * @param array-key $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->collection[$offset]);
    }

    /**
     * @param array-key $offset
     * @return Attributes|null
     */
    public function offsetGet($offset): ?Attributes
    {
        return $this->collection[$offset] ?? null;
    }

    /**
     * @param array-key|null $offset
     * @param Attributes $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->collection[] = $value;
            return;
        }
        $this->collection[$offset] = $value;
    }

    /**
     * @param array-key $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->collection[$offset]);
    }
}
