<?php


namespace EnjoysCMS\Core\Components\TwigExtension;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Setting extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting'])
        ];
    }

    public function getSetting(string $key)
    {
        return \EnjoysCMS\Core\Components\Helpers\Setting::get($key);
    }
}
