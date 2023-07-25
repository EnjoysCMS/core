<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Block\Annotation;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use EnjoysCMS\Core\Block\AbstractBlock;
use EnjoysCMS\Core\Block\Options;
use ReflectionClass;
use RuntimeException;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target("CLASS")
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class Widget implements Annotation
{

    private Options $options;
    /**
     * @var ReflectionClass<AbstractBlock>|null
     */
    private ?ReflectionClass $reflectionClass = null;

    public function __construct(
        private ?string $name = null,
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
