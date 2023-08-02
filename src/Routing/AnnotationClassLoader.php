<?php

declare(strict_types=1);


namespace EnjoysCMS\Core\Routing;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AnnotationClassLoader as BaseLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AnnotationClassLoader extends BaseLoader
{
    protected function configureRoute(Route $route, ReflectionClass $class, ReflectionMethod $method, $annot): void
    {
        if ('__invoke' === $method->getName()) {
            $route->setDefault('_controller', $class->getName());
        } else {
            $route->setDefault('_controller', [$class->getName(), $method->getName()]);
        }
    }

    protected function addRoute(
        RouteCollection $collection,
        object $annot,
        array $globals,
        \ReflectionClass $class,
        \ReflectionMethod $method
    ): void {
        parent::addRoute($collection, $annot, $globals, $class, $method);

        $name = $globals['name'].($annot->getName() ?? $this->getDefaultRouteName($class, $method));
        $route = $collection->get($name);

        if ($route === null){
            return;
        }

        $options = $route->getOptions() ?? [];
        $options['middlewares'] = array_merge($globals['options']['middlewares'] ?? [], $annot->getOptions()['middlewares'] ?? []);
        $options['groups'] = array_merge($globals['options']['groups'] ?? [], $annot->getOptions()['groups'] ?? []);
        $options['acl'] = (array_key_exists('acl', $options)) ? $options['acl'] : true;

        $route->setOptions($options);
    }
}
