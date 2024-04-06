<?php

declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<OrderCommentEntity>
 */
class OrderCommentCollection extends EntityCollection
{
    #[\Override]
    protected function getExpectedClass(): string
    {
        return OrderCommentEntity::class;
    }
}
