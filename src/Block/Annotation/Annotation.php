<?php

namespace EnjoysCMS\Core\Block\Annotation;

use EnjoysCMS\Core\Block\Options;
use ReflectionClass;

interface Annotation
{
    public function getOptions(): Options;

    public function getName(): string;

    public function getClassName(): string;

    public function setReflectionClass(ReflectionClass $reflectionClass): void;
}
