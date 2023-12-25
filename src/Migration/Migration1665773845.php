<?php

declare(strict_types=1);

namespace SptecOrderComments\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1665773845 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1665773845;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('ALTER TABLE `sptec_order_comment` ADD `task` TINYINT(1) NULL DEFAULT NULL;');
    }

    public function updateDestructive(Connection $connection): void {}
}
