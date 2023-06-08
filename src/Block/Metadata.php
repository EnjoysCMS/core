<?php

namespace EnjoysCMS\Core\Block;

use EnjoysCMS\Core\Block\Annotation\Block as BlockAnnotation;
use ReflectionClass;

class Metadata
{


    private string $name;
    /**
     * @var class-string<AbstractBlock>
     */
    private string $className;
    private Options $options;

    /**
     * @param ReflectionClass<AbstractBlock> $class
     * @param BlockAnnotation $annot
     */
    public function __construct(private ReflectionClass $class, private BlockAnnotation $annot)
    {
        $this->name = $this->annot->getName() ?? $this->class->getShortName();
        $this->className = $this->class->getName();
        $this->options = $this->annot->getOptions();

    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return class-string<AbstractBlock>
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

}
