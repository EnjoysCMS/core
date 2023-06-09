<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;


use ArrayAccess;
use ArrayIterator;
use Countable;
use EnjoysCMS\Core\Block\Annotation\Block as BlockAnnotation;
use IteratorAggregate;
use ReflectionClass;

/**
 * @implements  ArrayAccess<array-key, BlockAnnotation>
 * @implements  IteratorAggregate<array-key, BlockAnnotation>
 */
class Collection implements Countable, ArrayAccess, IteratorAggregate
{

    /**
     * @var BlockAnnotation[]
     */
    private array $collection = [];

    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * @return BlockAnnotation[]
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    public function addCollection(Collection $collection): void
    {
        /** @var BlockAnnotation $blockAnnotation */
        foreach ($collection as $blockAnnotation) {
            $this->addBlockAnnotation($blockAnnotation);
        }
    }

    public function addBlockAnnotation(BlockAnnotation $blockAnnotation): void
    {
        $this->collection[] = $blockAnnotation;
    }

    public function getBlockAnnotation(ReflectionClass $class): ?BlockAnnotation
    {
        foreach ($this->collection as $blockAnnotation) {
            if ($blockAnnotation->getClassName() === $class->getName()) {
                return $blockAnnotation;
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
     * @return BlockAnnotation|null
     */
    public function offsetGet($offset): ?BlockAnnotation
    {
        return $this->collection[$offset] ?? null;
    }

    /**
     * @param array-key|null $offset
     * @param BlockAnnotation $value
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
