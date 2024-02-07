<?php

use Doctrine\ORM\Mapping\Entity;
use EnjoysCMS\Core\Extensions\Doctrine\AutowiredInjectRepositoryFactory;
use EnjoysCMS\Core\Utils\Classes;
use Symfony\Component\Finder\Finder;

use function DI\factory;

$doctrineRepositoryDefinition = [];

$paths = [
    $_ENV['APP_DIR'],
    getenv('ROOT_PATH') . '/modules',
    getenv('ROOT_PATH') . '/vendor/enjoyscms',
];

$excludePaths = [
    'node_modules',
    'vendor',
    'tests'
];

$finder = new Finder();
$finder->files()
    ->name('/\.php$/')
    ->in($paths)
    ->exclude($excludePaths);

foreach ($finder as $file) {
    $class = (new Classes())->getClassNameByFilePath($file->getPathname());

    if ($class === false) {
        continue;
    }
    try {
        $reflectionClass = new ReflectionClass($class);
    } catch (ReflectionException) {
        continue;
    }

    if ($reflectionClass->isAbstract()) {
        continue;
    }

    if ([] === $attributes = $reflectionClass->getAttributes(Entity::class)) {
        continue;
    }

    $repositoryClassName = $attributes[0]->getArguments()['repositoryClass'] ?? false;

    if ($repositoryClassName === false) {
        continue;
    }

    if (!class_exists($repositoryClassName)) {
        continue;
    }


    $doctrineRepositoryDefinition[$repositoryClassName] = factory([
        AutowiredInjectRepositoryFactory::class,
        'getRepository'
    ])->parameter(
        'entityName',
        $reflectionClass->getName()
    );
}


return $doctrineRepositoryDefinition;
