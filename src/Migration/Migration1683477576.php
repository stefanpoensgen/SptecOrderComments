<?php

declare(strict_types=1);

namespace SptecOrderComments\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1683477576 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1683477576;
    }

    public function update(Connection $connection): void
    {
        $query = <<<SQL
            UPDATE `media_default_folder` SET `association_fields` = :associationFields WHERE `entity` = :entity;
            SQL;

        $connection->executeStatement($query, [
            'associationFields' => '["sptecOrderComments"]',
            'entity' => 'sptec_order_comment',
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
