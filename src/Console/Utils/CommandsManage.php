<?php

declare(strict_types=1);

namespace EnjoysCMS\Core\Console\Utils;

use Enjoys\Config\Config;
use Exception;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Yaml\Yaml;

final class CommandsManage
{
    private array $data = [];
    private string $filename;
    private bool $open = false;

    public function __construct(string $filename = null, private readonly ?Config $config = null)
    {
        $this->filename = $this->setFilename(
            $filename ?? $this->config?->get('console->filename') ?? getenv('ROOT_PATH') . '/console.yml'
        );
    }

    public function setFilename(string $filename): string
    {
        if (file_exists($filename)) {
            $this->data = Yaml::parseFile($filename) ?? [];
            $this->open = true;
        }
        return $filename;
    }

    /**
     * @throws Exception
     */
    public function save(): void
    {
        if ($this->open) {
            file_put_contents($this->filename, Yaml::dump($this->data));
        }
    }

    public function toValid(): void
    {
        foreach ($this->data as $command => $params) {
            if ($params === false) {
                continue;
            }
            try {
                new ReflectionClass($command);
            } catch (ReflectionException) {
                unset($this->data[$command]);
            }
        }
    }

    /**
     * @param array<string, array|null|false> $commands
     * @return string[]
     */
    public function registerCommands(array $commands = []): array
    {
        $registeredCommands = [];
        foreach ($commands as $command => $params) {
            $registeredCommands[] = $this->registerCommand($command, $params);
        }
        return array_filter($registeredCommands);
    }

    /**
     * @param string $command
     * @param false|array|null $params
     * @return ?string
     */
    public function registerCommand(string $command, null|false|array $params = null): ?string
    {
        $classname = $this->resolveClassName($command);
        if (!$this->has($classname)) {
            $this->data[$classname] = $params;
            return $classname;
        }
        return null;
    }

    /**
     * @param string[] $commands
     * @return string[]
     */
    public function unregisterCommands(array $commands = []): array
    {
        $unregisteredCommands = [];
        foreach ($commands as $command) {
            $unregisteredCommands[] = $this->unregisterCommand($command);
        }
        return array_filter($unregisteredCommands);
    }

    public function unregisterCommand(string $command = null): ?string
    {
        if ($command === null) {
            return null;
        }

        $classname = $this->resolveClassName($command);

        if ($this->has($classname)) {
            unset($this->data[$classname]);
            return $classname;
        }
        return null;
    }

    public function has(string $command): bool
    {
        return array_key_exists($command, $this->data);
    }

    private function resolveClassName(string $command): string
    {
        try {
            $classname = (new ReflectionClass($command))->getName();
        } catch (ReflectionException) {
            $classname = $command;
        }
        return $classname;
    }
}
