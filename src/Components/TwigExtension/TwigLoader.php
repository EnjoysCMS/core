<?php

namespace EnjoysCMS\Core\Components\TwigExtension;

use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;

/**
 * @deprecated use \EnjoysCMS\Core\Components\Extensions\Twig\TwigLoader
 */
class TwigLoader extends FilesystemLoader
{
    /**
     * @throws LoaderError
     */
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
