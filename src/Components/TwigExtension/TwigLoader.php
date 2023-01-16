<?php

namespace EnjoysCMS\Core\Components\TwigExtension;

use Twig\Loader\FilesystemLoader;

class TwigLoader extends FilesystemLoader
{
    protected function findTemplate(string $name, bool $throw = true)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if (is_file($name)) {
            $this->cache[$name] = $name;
            return $name;
        }

        return parent::findTemplate($name, $throw);
    }
}
