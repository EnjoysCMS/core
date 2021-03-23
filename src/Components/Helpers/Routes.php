<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Routes extends HelpersBase
{
    public static function getRouteCollection()
    {
        return self::$container->get(RouteCollection::class);
    }

    public static function getAllActiveControllers(): array
    {
        $activeRouteControllers = [];
        $rc = self::getRouteCollection();
        /** @var Route $route */
        foreach ($rc->getIterator() as $route) {
            $activeRouteControllers[] = implode('::', $route->getDefaults()['_controller']);
        }
        return $activeRouteControllers;
    }

}