<?php


namespace EnjoysCMS\Core\Components\TwigExtension;


use App\AppRouteCollection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class Modules extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getModules', [$this, 'getModules']),
            new TwigFunction('getApplicationAdminLinks', [$this, 'getApplicationAdminLinks']),

        ];
    }

    public function getApplicationAdminLinks()
    {
        $routes = new AppRouteCollection();

        return array_filter(
            $routes->getCollection()->getIterator()->getArrayCopy(),
            function ($r) {
                if (!empty($r->getOption('admin'))) {
                    return true;
                }
                return false;
            }
        );
    }

    public function getModules(): array
    {
        return array_filter(
            \EnjoysCMS\Core\Components\Helpers\Modules::installed(),
            function ($m) {
                if (!empty($m->adminLinks)) {
                    return true;
                }
                return false;
            }
        );
    }


}
