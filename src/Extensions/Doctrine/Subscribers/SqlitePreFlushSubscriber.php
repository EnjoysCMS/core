<?php

namespace EnjoysCMS\Core\Extensions\Doctrine\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;

/**
 * Foreign key checks are disabled by default on pdo_sqlite driver.
 *
 * As it's mentioned here:
 *
 * Foreign key constraints are disabled by default (for backwards compatibility),
 * so must be enabled separately for each database connection. (Note, however,
 * that future releases of SQLite might change so that foreign key constraints
 * enabled by default. Careful developers will not make any assumptions about
 * whether foreign keys are enabled by default but will instead enable
 * or disable them as necessary.
 *
 * $evm = new EventManager();
 * $evm->addEventSubscriber(new SqlitePreFlushSubscriber());
 *
 * @deprecated use middleware \Doctrine\DBAL\Driver\AbstractSQLiteDriver\Middleware\EnableForeignKeys,
 */
final class SqlitePreFlushSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [Events::preFlush];
    }

    /**
     * @throws Exception
     */
    public function preFlush(PreFlushEventArgs $args): void
    {
        $connection = $args->getObjectManager()->getConnection();

        if ($connection->getDatabasePlatform() instanceof SqlitePlatform) {
            $connection->executeStatement('PRAGMA foreign_keys = ON;');
        }
    }

}
