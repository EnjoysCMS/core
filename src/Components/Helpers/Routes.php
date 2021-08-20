<?php


namespace EnjoysCMS\Core\Components\Helpers;


use Symfony\Component\Routing\Route;

class Routes extends HelpersBase
{
    public static function getRouteCollection()
    {
        return self::$container->get('Router')->getRouteCollection();
    }

    public static function getAllActiveControllers(): array
    {
        $activeRouteControllers = [];
        $rc = self::getRouteCollection();

        /** @var Route $route */
        foreach ($rc->getIterator() as $route) {
            $controller = $route->getDefaults()['_controller'];
            if(is_array($controller)){
                $controller = implode('::', $route->getDefaults()['_controller']);
            }
            $activeRouteControllers[] = $controller;
        }
        return $activeRouteControllers;
    }

}
