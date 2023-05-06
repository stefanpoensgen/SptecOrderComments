<?php

declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<OrderCommentMediaEntity>
 */
class OrderCommentMediaCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return OrderCommentMediaEntity::class;
    }
}
