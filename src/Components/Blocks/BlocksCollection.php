<?php

namespace EnjoysCMS\Core\Components\Blocks;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Symfony\Component\Config\Resource\ResourceInterface;

class BlocksCollection implements IteratorAggregate, Countable
{
    private array $blocks = [];

    /**
     * @var array<string, ResourceInterface>
     */
    private array $resources = [];

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->blocks);
    }

    public function count(): int
    {
        return count($this->blocks);
    }


    public function addCollection(BlocksCollection $collection): void
    {
        foreach ($collection as $block) {
            $this->blocks = $block;
        }
        foreach ($collection->getResources() as $resource) {
            $this->addResource($resource);
        }
    }


    public function getResources(): array
    {
        return $this->resources;
    }

    public function addResource(ResourceInterface $resource): void
    {
        $key = (string)$resource;

        if (!isset($this->resources[$key])) {
            $this->resources[$key] = $resource;
        }
    }

}
