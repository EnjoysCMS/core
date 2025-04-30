<?php

namespace EnjoysCMS\Core\Extensions\Composer;

use Composer\Autoload\ClassLoader;
use Composer\Semver\Comparator;
use stdClass;

use function json_decode;

class Utils
{
    public static function findFilePathByClassName(string $className): bool|string
    {
        $loaders = ClassLoader::getRegisteredLoaders();

        foreach ($loaders as $loader) {
            if (false !== $path = $loader->findFile($className)) {
                return $path;
            }
        }
        return false;
    }

    /**
     * @return list<string>
     */
    public static function getLoadersList(): array
    {
        $loaders = ClassLoader::getRegisteredLoaders();
        $list = [];
        foreach (array_keys($loaders) as $dir) {
            /** @var string $dir */
            $list[] = $dir;
        }
        return $list;
    }

    public static function getDirByPackage(string $packageName): bool|string
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

    public static function parseComposerJson($composerJsonFile): stdClass
    {
        $json = json_decode(file_get_contents($composerJsonFile));

        $object = new stdClass();

        $object->packageName = $json->name;
        $object->type = $json->type ?? null;
        $object->scripts = $json->scripts ?? [];
        $object->installPath = Utils::getDirByPackage($object->packageName);

        $object->description = $json->description ?? null;
        $object->extra = $json->extra ?? null;
        $object->type = $json->type ?? null;

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
