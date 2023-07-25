<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;


use ArrayAccess;
use ArrayIterator;
use Countable;
use EnjoysCMS\Core\Block\Annotation\Annotation;
use IteratorAggregate;
use ReflectionClass;

/**
 * @implements  ArrayAccess<array-key, Annotation>
 * @implements  IteratorAggregate<array-key, Annotation>
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{

    /**
     * @var Annotation[]
     */
    private array $collection = [];

    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @return Annotation[]
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function addCollection(Collection $collection): void
    {
        /** @var Annotation $annotation */
        foreach ($collection as $annotation) {
            $this->addAnnotation($annotation);
        }
    }

    public function addAnnotation(Annotation $annotation): void
    {
        $this->collection[] = $annotation;
    }

    public function getAnnotation(ReflectionClass $class): ?Annotation
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
     * @return Annotation|null
     */
    public function offsetGet($offset): ?Annotation
    {
        return $this->collection[$offset] ?? null;
    }

    /**
     * @param array-key|null $offset
     * @param Annotation $value
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
