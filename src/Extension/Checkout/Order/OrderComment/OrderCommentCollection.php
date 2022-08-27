<?php

declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                    add(OrderCommentEntity $entity)
 * @method void                    set(string $key, OrderCommentEntity $entity)
 * @method OrderCommentEntity[]    getIterator()
 * @method OrderCommentEntity[]    getElements()
 * @method OrderCommentEntity|null get(string $key)
 * @method OrderCommentEntity|null first()
 * @method OrderCommentEntity|null last()
 */
class OrderCommentCollection extends EntityCollection
{
    public function getExpectedClass(): string
    {
        return OrderCommentEntity::class;
    }
}
