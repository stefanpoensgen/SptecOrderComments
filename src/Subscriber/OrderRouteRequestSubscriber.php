<?php declare(strict_types=1);

namespace SptecOrderComments\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Storefront\Event\RouteRequest\OrderRouteRequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderRouteRequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OrderRouteRequestEvent::class => 'onOrderRouteRequest',
        ];
    }

    public function onOrderRouteRequest(OrderRouteRequestEvent $event): void
    {
        $criteria = $event->getCriteria();
        $criteria->addAssociation('sptecOrderComments');
        $criteria->addAssociation('sptecOrderComments.media');
        $criteria->getAssociation('sptecOrderComments')->addFilter(new EqualsFilter('internal', false));
    }
}
