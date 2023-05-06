<?php

declare(strict_types=1);

namespace SptecOrderComments;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class SptecOrderComments extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->container->get(Connection::class)->executeStatement('DROP TABLE IF EXISTS sptec_order_comment_media');
        $this->container->get(Connection::class)->executeStatement('DROP TABLE IF EXISTS sptec_order_comment');
    }
}
