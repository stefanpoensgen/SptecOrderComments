<?php

declare(strict_types=1);

namespace SptecOrderComments\Core\Checkout\Order\Event;

class CheckoutOrderCommentUpdatedEvent extends CheckoutOrderCommentCreatedEvent
{
    public const EVENT_NAME = 'checkout.order.comment.updated';

    #[\Override]
    public function getName(): string
    {
        return self::EVENT_NAME;
    }
}
