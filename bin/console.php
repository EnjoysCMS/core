<?php

use Enjoys\Config\Config;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Yaml\Yaml;

$autoloadFiles = [
    __DIR__ . '/../../../../bootstrap.php',
    __DIR__ . '/../../../bootstrap.php',
    __DIR__ . '/../../bootstrap.php',
    __DIR__ . '/../bootstrap.php',
    __DIR__ . '/bootstrap.php',
];

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        break;
    }
}

$application = new Application();

/** @var ContainerInterface $container */

$config = new Config();

try {
    $config->addConfig(
        $container->get(Config::class)->get('console->filename'),
        ['flags' => Yaml::PARSE_CONSTANT],
        Config::YAML
    );

    foreach ($config->getConfig() as $class => $params) {
        if ($params === false) {
            continue;
        }

        if ($params === null) {
            $params = [];
        }

        if (!class_exists($class)) {
            require $params['_include_path'] ?? throw new \InvalidArgumentException(
                sprintf(
                    'The class `%s` cannot be loaded, try specifying the path
                    to the file in the `_include_path` parameter',
                    $class
                )
            );
        }

        $application->add($container->make($class, $params));
    }
    $application->run();
} catch (Throwable $e) {
    die($e);
}
