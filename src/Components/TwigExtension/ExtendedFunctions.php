<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Components\TwigExtension;

use Exception;
use ReflectionClass;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

use function base64_decode;
use function base64_encode;

/**
 * @deprecated use \EnjoysCMS\Core\Components\Extensions\Twig\CoreTwigExtension
 */
final class ExtendedFunctions extends AbstractExtension
{
    public function getTests(): array
    {
        return array(
            new TwigTest('instanceof', array($this, 'isInstanceOf'))
        );
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('callstatic', function ($class_method_string, ...$args) {
                list($class, $method) = explode('::', $class_method_string);
                if (!class_exists($class)) {
                    throw new Exception("Cannot call static method $method on Class $class: Invalid Class");
                }
                if (!method_exists($class, $method)) {
                    throw new Exception("Cannot call static method $method on Class $class: Invalid method");
                }
                return forward_static_call_array([$class, $method], $args);
            })
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('regex_replace', [$this, 'regexReplace'], ['is_safe' => ['all']]),
            new TwigFilter('base64_decode', [$this, 'base64Decode'], ['is_safe' => ['all']]),
            new TwigFilter('base64_encode', [$this, 'base64Encode'], ['is_safe' => ['all']]),
        ];
    }

    public function base64Decode(string $body, bool $strict = false): bool|string
    {
        return base64_decode($body, $strict);
    }

    public function base64Encode(string $body): string
    {
        return base64_encode($body);
    }

    public function regexReplace(?string $body, string $pattern, string $replacement): array|string|null
    {
        return preg_replace($pattern, $replacement, $body);
    }

    public function isInstanceOf($object, $class): bool
    {
        $reflectionClass = new ReflectionClass($class);

        return $reflectionClass->isInstance($object);
    }
}
