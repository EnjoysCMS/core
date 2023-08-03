<?php

namespace EnjoysCMS\Core\Routing;

use Exception;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

class OverridingRoutesParams
{
    private RouteCollection $rewriteRoutes;

    /**
     * @throws Exception
     */
    public function __construct(
        string $resource,
        FileLoader $loader,
        private readonly RouteCollection $routeCollection,
    ) {
        try {
            $this->rewriteRoutes = $loader->load($resource);
        } catch (FileLocatorFileNotFoundException) {
            $this->rewriteRoutes = new RouteCollection();
        }
    }

    public function getOverridingRouteCollection(): RouteCollection
    {
        foreach ($this->rewriteRoutes as $name => $rewriteRoute) {
            $route = $this->routeCollection->get($name);
            //Если route не существует, или если определен controller - пропускаем все манипуляции
            if ($route === null || $rewriteRoute->getDefault('_controller') !== null) {
                continue;
            }

            //Перезаписываем только те значения, которые записаны в yaml файле
            $route->setPath($rewriteRoute->getPath());
            if ($rewriteRoute->getHost() !== '') {
                $route->setHost($rewriteRoute->getHost());
            }
            if ($rewriteRoute->getSchemes() !== []) {
                $route->setSchemes($rewriteRoute->getSchemes());
            }
            if ($rewriteRoute->getMethods() !== []) {
                $route->setMethods($rewriteRoute->getMethods());
            }
            if ($rewriteRoute->getDefaults() !== []) {
                foreach (array_keys($route->getDefaults()) as $key) {
                    if ($rewriteRoute->getDefault($key) === null) {
                        $rewriteRoute->setDefault($key, $route->getDefault($key));
                    }
                }
                $route->setDefaults($rewriteRoute->getDefaults());
            }
            if ($rewriteRoute->getRequirements() !== []) {
                foreach (array_keys($route->getRequirements()) as $key) {
                    if ($rewriteRoute->getRequirement($key) === null) {
                        $rewriteRoute->setRequirement($key, $route->getRequirement($key));
                    }
                }
                $route->setRequirements($rewriteRoute->getRequirements());
            }
            if ($rewriteRoute->getOptions() !== []) {
                foreach (array_keys($route->getOptions()) as $key) {
                    if ($rewriteRoute->getOption($key) === null) {
                        $rewriteRoute->setOption($key, $route->getOption($key));
                    }
                }
                $route->setOptions($rewriteRoute->getOptions());
            }
            if ($rewriteRoute->getCondition() !== '') {
                $route->setCondition($rewriteRoute->getCondition());
            }

            $this->rewriteRoutes->add($name, $route);
        }

        return $this->rewriteRoutes;
    }
}
