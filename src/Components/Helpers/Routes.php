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
            $activeRouteControllers[] = implode('::', (array)$route->getDefault('_controller'));
        }
        return $activeRouteControllers;
    }
}
