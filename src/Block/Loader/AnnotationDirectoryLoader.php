<?php

namespace EnjoysCMS\Core\Block\Loader;


use EnjoysCMS\Core\Block\Collection;
use Exception;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Symfony\Component\Config\Resource\DirectoryResource;

use function is_string;


class AnnotationDirectoryLoader extends AnnotationFileLoader
{
    /**
     * @throws InvalidArgumentException When the directory does not exist or its routes cannot be parsed
     * @throws ReflectionException
     */
    public function load(mixed $resource, string $type = null): ?Collection
    {
        $collection = new Collection();
        if (!is_dir($dir = $this->locator->locate($resource))) {
            return parent::supports($resource, $type) ? parent::load($resource, $type) : $collection;
        }

        $collection->addResource(new DirectoryResource($dir, '/\.php$/'));

        /** @var SplFileInfo[] $files */
        $files = iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveCallbackFilterIterator(
                    new RecursiveDirectoryIterator(
                        $dir,
                        FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS
                    ),
                    function (SplFileInfo $current) {
                        if (in_array($current->getBasename(), ['node_modules', 'vendor'], true)) {
                            return false;
                        }
                        return !str_starts_with($current->getBasename(), '.');
                    }
                ),
                RecursiveIteratorIterator::LEAVES_ONLY
            )
        );


        usort($files, function (SplFileInfo $a, SplFileInfo $b) {
            return (string)$a > (string)$b ? 1 : -1;
        });

        foreach ($files as $file) {
            if (!$file->isFile() || !str_ends_with($file->getFilename(), '.php')) {
                continue;
            }

            if ($class = $this->findClass($file->__toString())) {
                $reflectionClass = new ReflectionClass($class);
                if ($reflectionClass->isAbstract()) {
                    continue;
                }

                $collection->addCollection($this->loader->load($class, $type));
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(mixed $resource, string $type = null): bool
    {
        if ('annotation' === $type) {
            return true;
        }

        if ($type || !is_string($resource)) {
            return false;
        }

        try {
            return is_dir($this->locator->locate($resource));
        } catch (Exception $e) {
            return false;
        }
    }
}
