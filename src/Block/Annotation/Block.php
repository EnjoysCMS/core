<?php

namespace EnjoysCMS\Core\Block\Annotation;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Block
{

    public function __construct(
        private string $name,
        private ?string $template = null,
        private array $options = []
    ) {
        $this->options['template'] = [
            'value' => $this->template
        ];
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
