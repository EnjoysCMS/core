<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block;

use Attribute;
use ReflectionClass;
use RuntimeException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class BlockAttributes implements Attributes
{

    private Options $options;
    /**
     * @var ReflectionClass<AbstractBlock>|null
     */
    private ?ReflectionClass $reflectionClass = null;

    public function __construct(
        private readonly ?string $name = null,
        array $options = [],
    ) {
        $this->options = Options::createFromArray($options);
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getName(): string
    {
        return $this->name ?? $this->reflectionClass?->getShortName() ?? throw new RuntimeException(
            ''
        );
    }


    /**
     * @return class-string<AbstractBlock>
     */
    public function getClassName(): string
    {
        return $this->reflectionClass?->getName() ?? throw new RuntimeException(
            ''
        );
    }

    /**
     * @param ReflectionClass<AbstractBlock> $reflectionClass
     * @return void
     */
    public function setReflectionClass(ReflectionClass $reflectionClass): void
    {
        $this->reflectionClass = $reflectionClass;
    }
}
