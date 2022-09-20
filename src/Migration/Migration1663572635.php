<?php declare(strict_types=1);

namespace SptecOrderComments\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663572635 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1663572635;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
CREATE TABLE `sptec_order_comment_media` (
    `id` BINARY(16) NOT NULL,
    `version_id` BINARY(16) NOT NULL,
    `order_comment_id` BINARY(16) NOT NULL,
    `order_comment_version_id` BINARY(16) NOT NULL,
    `media_id` BINARY(16) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    INDEX IDX_D3FA2EC18D9F8D38F867DA50 (order_comment_id, order_comment_version_id),
    PRIMARY KEY (`id`, `version_id`),
    CONSTRAINT `fk.sptec_order_comment_media.media_id` FOREIGN KEY (`media_id`)
    REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.sptec_order_comment_media.order_comment_id` FOREIGN KEY (`order_comment_id`, `order_comment_version_id`)
    REFERENCES `sptec_order_comment` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($query);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
