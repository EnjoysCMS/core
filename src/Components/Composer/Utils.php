<?php


namespace EnjoysCMS\Core\Components\Composer;

use Composer\Autoload\ClassLoader;

class Utils
{
    /**
     * @param  string $classname
     * @return false|mixed|string
     */
    public static function findFilePathByClassName(string $classname)
    {
        $loaders = ClassLoader::getRegisteredLoaders();

        foreach ($loaders as $loader) {
            if (false !== $path = $loader->findFile($classname)) {
                return $path;
            }
        }
        return false;
    }

    public static function getLoadersList(): array
    {
        $loaders = ClassLoader::getRegisteredLoaders();
        $list = [];
        foreach ($loaders as $dir => $loader) {
            $list[] = $dir;
        }
        return $list;
    }

    public static function getDirByPackage(string $packageName)
    {
        $loadersDirectories = self::getLoadersList();
        $composerInstalledFile = null;
        foreach ($loadersDirectories as $directory) {
            $composerInstalledFile = $directory . '/composer/installed.json';
            if (file_exists($composerInstalledFile)) {
                break;
            }
            $composerInstalledFile = null;
        }

        if ($composerInstalledFile === null) {
            return false;
        }

        $installedPackages = json_decode(file_get_contents($composerInstalledFile));
        foreach ($installedPackages->packages as $key => $package) {
            if ($package->name === $packageName) {
                return realpath(pathinfo($composerInstalledFile, PATHINFO_DIRNAME) . '/' . $package->{'install-path'});
            }
        }
        return false;
    }

    public static function parseComposerJson($composerJsonFile)
    {
        $json = \json_decode(file_get_contents($composerJsonFile));

        $object = new \stdClass();

        $object->packageName = $json->name;
        $object->installPath = Utils::getDirByPackage($object->packageName);

        $object->description = (isset($json->description)) ? $json->description : null;
        $object->extra = (isset($json->extra)) ? $json->extra : [];

        foreach ($json->autoload->{'psr-4'} as $namespace => $path) {
            $object->namespaces[] = $namespace;
            $object->paths[] = [
                'namespace' => $namespace,
                'path' => $object->installPath . '/' . $path
            ];
        }

        return $object;
    }
}
