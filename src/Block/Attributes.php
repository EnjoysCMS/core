<?php

namespace EnjoysCMS\Core\Block;

use ReflectionClass;

interface Attributes
{
    public function getOptions(): Options;

    public function getName(): string;

    public function getClassName(): string;

    public function setReflectionClass(ReflectionClass $reflectionClass): void;
}
