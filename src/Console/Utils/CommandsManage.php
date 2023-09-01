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
     * @return void
     */
    public function registerCommands(array $commands = []): void
    {
        foreach ($commands as $command => $params) {
            $this->registerCommand($command, $params);
        }
    }

    /**
     * @param string $command
     * @param false|array|null $params
     * @return void
     */
    public function registerCommand(string $command, null|false|array $params = null): void
    {
        $classname = $this->resolveClassName($command);
        if (!$this->has($classname)) {
            $this->data[$classname] = $params;
        }
    }

    /**
     * @param string[] $commands
     * @return void
     */
    public function unregisterCommands(array $commands = []): void
    {
        foreach ($commands as $command) {
            $this->unregisterCommand($command);
        }
    }

    public function unregisterCommand(string $command = null): void
    {
        if ($command === null) {
            return;
        }

        $classname = $this->resolveClassName($command);

        if ($this->has($classname)) {
            unset($this->data[$classname]);
        }
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
