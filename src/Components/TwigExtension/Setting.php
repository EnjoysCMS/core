<?php


namespace App\Components\TwigExtension;


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
        return \App\Components\Helpers\Setting::get($key);
    }
}
