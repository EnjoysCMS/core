<?php

namespace EnjoysCMS\Core\Routing;

use Enjoys\Config\Config;
use Symfony\Component\Routing\RouteCollection;

class ConfigureGroupsRouter
{
    public function __construct(
        private readonly RouteCollection $routeCollection,
        private readonly Config $config
    ) {
    }

    public function apply(): void
    {
        foreach ($this->config->get('router->groups', []) as $group => $options) {
            foreach ($this->routeCollection as $route) {
                if (!in_array($group, $route->getOption('groups') ?? [])) {
                    continue;
                }
                foreach ($options ?? [] as $option => $data) {
                    if (!in_array(
                        $option,
                        $this->config->get('router->allowed_change_group_options', [
                            'middlewares',
                            'acl'
                        ])
                    )) {
                        continue;
                    }

                    $routeOptions = $route->getOptions();

                    if (is_array($route->getOption($option))) {
                        $routeOptions[$option] = array_merge($route->getOption($option), $data ?? []);
                    } else {
                        $routeOptions[$option] = $data;
                    }
                    $route->setOptions($routeOptions);
                }
            }
        }
    }
}
