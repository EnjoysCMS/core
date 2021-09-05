<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

final class ExtendedFunctions extends AbstractExtension
{
    public function getTests()
    {
        return array(
            new TwigTest('instanceof', array($this, 'isInstanceOf'))
        );
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('regex_replace', [$this, 'regexReplace'], ['is_safe' => ['all']])
        ];
    }

    public function regexReplace(?string $body, string $pattern, string $replacement)
    {
        return preg_replace($pattern, $replacement, $body);
    }

    public function isInstanceOf($object, $class)
    {
        $reflectionClass = new \ReflectionClass($class);

        return $reflectionClass->isInstance($object);
    }
}