<?php

declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentCreatedEvent;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentUpdatedEvent;
use SptecOrderComments\Core\Checkout\Order\Subscriber\BusinessEventCollectorSubscriber;

class BusinessEventCollectorSubscriberTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            [
                BusinessEventCollectorEvent::NAME => ['onCheckoutOrderCommentCreatedEvent', 1000],
            ],
            BusinessEventCollectorSubscriber::getSubscribedEvents()
        );
    }

    public function testOnCheckoutOrderCommentCreatedEvent(): void
    {
        $context = new Context(new SystemSource());
        $businessEventCollector = $this->getContainer()->get(BusinessEventCollector::class);
        $result = $businessEventCollector->collect($context);

        $businessEvent = $result->get(CheckoutOrderCommentCreatedEvent::EVENT_NAME);
        static::assertSame(CheckoutOrderCommentCreatedEvent::class, $businessEvent->getClass());

        $businessEvent = $result->get(CheckoutOrderCommentUpdatedEvent::EVENT_NAME);
        static::assertSame(CheckoutOrderCommentUpdatedEvent::class, $businessEvent->getClass());
    }
}
