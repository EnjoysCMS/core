<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Extensions\Twig;


use Closure;
use InvalidArgumentException;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class Callback
{

    public static function getUndefinedFilterCallback(array $callbacks): Closure
    {
        return function (string $name) use ($callbacks): TwigFilter|false {
            if ($callbacks !== [] && array_is_list($callbacks)) {
                throw new InvalidArgumentException(
                    'Configuration filters should be an associative array, where the key is the filter
                         name, the value is a callback (php function, class static method, class method(needs a runtime
                         implementation) or null, if the value is null, then the key will be a callback function.'
                );
            }
            if (array_key_exists($name, $callbacks)) {
                return new TwigFilter($name, $callbacks[$name] ?? $name);
            }
            return false;
        };
    }

    public static function getUndefinedFunctionCallback(array $callbacks): Closure
    {
        return function (string $name) use ($callbacks): TwigFunction|false {
            if ($callbacks !== [] && array_is_list($callbacks)) {
                throw new InvalidArgumentException(
                    'Configuration functions should be an associative array, where the key is the function
                        name, the value is a callback (php function, class static method, class method(needs a runtime
                        implementation) or null, if the value is null, then the key will be a callback function'
                );
            }
            if (array_key_exists($name, $callbacks)) {
                return new TwigFunction($name, $callbacks[$name] ?? $name);
            }
            return false;
        };
    }
}
