<?php

namespace EnjoysCMS\Core\Block;

class Block
{


    public function __construct(private string $className, private string $name, private BlockOptions $options)
    {
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function setOptions(BlockOptions $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): BlockOptions
    {
        return $this->options;
    }

}
