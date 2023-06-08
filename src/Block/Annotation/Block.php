<?php

namespace EnjoysCMS\Core\Block\Annotation;

use Attribute;
use EnjoysCMS\Core\Block\Options;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Block
{

    private Options $options;

    public function __construct(
        private ?string $name = null,
        array $options = []
    ) {
        $this->options = Options::createFromArray($options);
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
