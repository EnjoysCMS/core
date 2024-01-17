<?php

namespace EnjoysCMS\Core\Setting;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Setting implements \ArrayAccess
{

    private static ?array $cache = null;

    public function __construct(
        private readonly EntityManager $em,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
    }

    /**
     * @throws NotSupported
     */
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
     * @throws NotSupported
     */
    private function fetchSetting(): array
    {
        /** @var Repository\Setting $repository */
        $repository = $this->em->getRepository(Entity\Setting::class);
        return $repository->findAllKeyVar();
    }

    /**
     * @throws NotSupported
     */
    public function offsetExists(mixed $offset): bool
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        return isset(self::$cache[$offset]) || array_key_exists($offset, self::$cache);
    }

    /**
     * @throws NotSupported
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @throws NotSupported
     */
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

    /**
     * @throws NotSupported
     */
    public function offsetUnset(mixed $offset): void
    {
        if (static::$cache === null) {
            static::$cache = $this->fetchSetting();
        }

        unset(self::$cache[$offset]);
    }
}
