<?php

namespace EnjoysCMS\Core\Block;


use Countable;
use Symfony\Component\Config\Resource\ResourceInterface;

class BlockCollection implements Countable
{

    /**
     * @var Block[]
     */
    private array $collection;

    /**
     * @var ResourceInterface[]
     */
    private array $resources = [];

    public function __construct($collection = [])
    {
        $this->collection = $collection;
    }


    public function count(): int
    {
        return count($this->collection);
    }

    public function addResource(ResourceInterface $resource): void
    {
        $this->resources[] = $resource;
    }


    public function getResources(): array
    {
        return $this->resources;
    }

    public function addCollection(BlockCollection $collection): void
    {
        foreach ($collection->getCollection() as $block) {
            $this->addBlock($block);
        }
        foreach ($collection->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function addBlock(Block $block): void
    {
        $this->collection[] = $block;
    }


}
