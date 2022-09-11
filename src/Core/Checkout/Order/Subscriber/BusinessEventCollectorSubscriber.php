<?php declare(strict_types=1);

namespace SptecOrderComments\Core\Checkout\Order\Subscriber;

use Shopware\Core\Framework\Event\BusinessEventCollector;
use Shopware\Core\Framework\Event\BusinessEventCollectorEvent;
use Shopware\Core\Framework\Event\BusinessEventDefinition;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentCreatedEvent;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BusinessEventCollectorSubscriber implements EventSubscriberInterface
{
    private BusinessEventCollector $businessEventCollector;

    public function __construct(BusinessEventCollector $businessEventCollector)
    {
        $this->businessEventCollector = $businessEventCollector;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BusinessEventCollectorEvent::NAME => ['onCheckoutOrderCommentCreatedEvent', 1000],
        ];
    }

    public function onCheckoutOrderCommentCreatedEvent(BusinessEventCollectorEvent $event): void
    {
        $collection = $event->getCollection();

        $definition = $this->businessEventCollector->define(CheckoutOrderCommentCreatedEvent::class);

        if ($definition instanceof BusinessEventDefinition) {
            $collection->set($definition->getName(), $definition);
        }

        $definition = $this->businessEventCollector->define(CheckoutOrderCommentUpdatedEvent::class);

        if ($definition instanceof BusinessEventDefinition) {
            $collection->set($definition->getName(), $definition);
        }
    }
}
