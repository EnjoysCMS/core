<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Routing;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AttributeClassLoader as BaseLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AttributeClassLoader extends BaseLoader
{

    #[\Override]
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, $attr): void
    {
        if ('__invoke' === $method->getName()) {
            $route->setDefault('_controller', $class->getName());
        } else {
            $route->setDefault('_controller', [$class->getName(), $method->getName()]);
        }
    }

    /**
     * @psalm-suppress UndefinedMethod
     */
    #[\Override]
    protected function addRoute(
        RouteCollection $collection,
        object $attr,
        array $globals,
        \ReflectionClass $class,
        \ReflectionMethod $method
    ): void {
        parent::addRoute($collection, $attr, $globals, $class, $method);

        $name = $globals['name'].($attr->getName() ?? $this->getDefaultRouteName($class, $method));
        $route = $collection->get($name);

        if ($route === null){
            return;
        }

        $options = $route->getOptions() ?? [];
        $options['middlewares'] = array_merge($globals['options']['middlewares'] ?? [], $attr->getOptions()['middlewares'] ?? []);
        $options['groups'] = array_merge($globals['options']['groups'] ?? [], $attr->getOptions()['groups'] ?? []);
        $options['acl'] = (array_key_exists('acl', $options)) ? $options['acl'] : true;

        $route->setOptions($options);
    }
}
