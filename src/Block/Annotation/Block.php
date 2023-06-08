<?php

namespace EnjoysCMS\Core\Block\Annotation;

use Attribute;
use EnjoysCMS\Core\Block\BlockOptions;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Block
{

    private BlockOptions $options;

    public function __construct(
        private ?string $name = null,
        array $options = []
    ) {
        $this->options = BlockOptions::createFromArray($options);
    }

    public function getOptions(): BlockOptions
    {
        return $this->options;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
