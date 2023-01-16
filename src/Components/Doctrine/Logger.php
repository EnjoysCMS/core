<?php

namespace EnjoysCMS\Core\Components\Doctrine;

use Doctrine\DBAL\Logging\SQLLogger;
use Psr\Log\LoggerInterface;

/**
 * @deprecated
 */
class Logger implements SQLLogger
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    private ?string $query = null;
    private ?array $params = null;
    private ?array $types = null;

    private float $allTime = 0;
    private int $countQuery = 0;

    /**
     * @var float|string
     */
    private $startTime = 0;

    public function __construct(LoggerInterface $logger)
    {
        trigger_deprecation(
            'enjoyscms/core',
            '4.3.5',
            'In the future will be removed.'
        );
        $this->logger = $logger;
    }

    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->query = $sql;
        $this->params = $params;
        $this->types = $types;
        $this->startTime = microtime(true);
        $this->countQuery++;
    }

    public function stopQuery()
    {
        $endtime = (microtime(true) - $this->startTime);
        $ms = round($endtime * 1000);

        $this->logger->debug(
            "#{$this->countQuery} [{$this->allTime}ms +{$ms}ms] {$this->query}",
            [
                'params' => $this->params,
                'types' => $this->types,
                'time' => $endtime,
            ]
        );
        $this->allTime = $this->allTime + $ms;
    }

    private function clear()
    {
        $this->query = null;
        $this->params = null;
        $this->types = null;
    }
}
