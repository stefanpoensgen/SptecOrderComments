<?php

declare(strict_types=1);

namespace SptecOrderComments\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1661526504 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661526504;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            CREATE TABLE sptec_order_comment (
                id BINARY(16) NOT NULL,
                version_id BINARY(16) NOT NULL,
                order_id BINARY(16) NOT NULL,
                order_version_id BINARY(16) NOT NULL,
                created_by_id BINARY(16) NULL,
                content LONGTEXT NOT NULL,
                internal TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                INDEX IDX_D3FA2EC18D9F6D38F866DA50 (order_id, order_version_id),
                INDEX IDX_D3FA2EC1B03A8386 (created_by_id),
                PRIMARY KEY(`id`, `version_id`),
                CONSTRAINT `fk.sptec_order_comment.order_id` FOREIGN KEY (`order_id`, `order_version_id`)
                REFERENCES `order` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                CONSTRAINT `fk.sptec_order_comment.created_by_id` FOREIGN KEY (`created_by_id`)
                REFERENCES `user` (`id`) ON UPDATE CASCADE ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement('DROP TABLE `sptec_order_comment`');
    }
}
