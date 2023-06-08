<?php

namespace EnjoysCMS\Core\Block;


use Countable;
use Symfony\Component\Config\Resource\ResourceInterface;

class Collection implements Countable
{

    /**
     * @var Metadata[]
     */
    private array $collection = [];

    /**
     * @var ResourceInterface[]
     */
    private array $resources = [];


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

    public function addCollection(Collection $collection): void
    {
        foreach ($collection->getCollection() as $block) {
            $this->addMetadata($block);
        }
        foreach ($collection->getResources() as $resource) {
            $this->addResource($resource);
        }
    }

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function addMetadata(Metadata $metadata): void
    {
        $this->collection[] = $metadata;
    }


}
