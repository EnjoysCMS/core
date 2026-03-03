<?php

namespace EnjoysCMS\Core\Setting;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Setting implements \ArrayAccess
{

    private static ?array $cache = null;

    public function __construct(
        private readonly Repository\Setting $repository,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
    }

    public function get(string $var, mixed $default = null): mixed
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        if (array_key_exists($var, static::$cache)) {
            return static::$cache[$var];
        }

        $this->logger->debug(sprintf('Parameter `%s` not found! Return default value', $var));

        return $default;
    }

    /**
     * @return array<string, null|string>
     * @psalm-suppress MixedInferredReturnType, MixedReturnStatement
     */
    private function fetchSetting(): array
    {
        return $this->repository->findAllKeyVar();
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        return isset(self::$cache[$offset]) || array_key_exists($offset, self::$cache);
    }

    #[\Override]
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        if ($offset === null) {
            self::$cache[] = $value;
            return;
        }

        self::$cache[$offset] = $value;
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        unset(self::$cache[$offset]);
    }
}
