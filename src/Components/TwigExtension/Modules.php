<?php


namespace EnjoysCMS\Core\Components\TwigExtension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;


class Modules extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getModules', [$this, 'getModules'])

        ];
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
