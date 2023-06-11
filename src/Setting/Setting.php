<?php

namespace EnjoysCMS\Core\Setting;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Setting
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

}
