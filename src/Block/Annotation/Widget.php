<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Annotation;

use Attribute;
use EnjoysCMS\Core\Block\AbstractBlock;
use EnjoysCMS\Core\Block\Options;
use ReflectionClass;
use RuntimeException;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Widget implements Attributes
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

    #[\Override]
    public function getOptions(): Options
    {
        return $this->options;
    }

    #[\Override]
    public function getName(): string
    {
        return $this->name ?? $this->reflectionClass?->getShortName() ?? throw new RuntimeException(
            ''
        );
    }


    /**
     * @return class-string<AbstractBlock>
     */
    #[\Override]
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
    #[\Override]
    public function setReflectionClass(ReflectionClass $reflectionClass): void
    {
        $this->reflectionClass = $reflectionClass;
    }
}
