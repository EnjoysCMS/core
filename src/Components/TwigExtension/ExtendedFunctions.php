<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigTest;

final class ExtendedFunctions extends AbstractExtension
{
    public function getTests()
    {
        return array(
            new TwigTest('instanceof', array($this, 'isInstanceOf'))
        );
    }

    public function isInstanceOf($object, $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        return $reflectionClass->isInstance($object);
    }
}