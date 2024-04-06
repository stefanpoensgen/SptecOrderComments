<?php

declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\Test\TestDefaults;
use Shopware\Storefront\Event\RouteRequest\OrderRouteRequestEvent;
use SptecOrderComments\Subscriber\OrderRouteRequestSubscriber;
use Symfony\Component\HttpFoundation\Request;

class OrderRouteRequestTest extends TestCase
{
    use IntegrationTestBehaviour;

    private OrderRouteRequestSubscriber $orderRouteRequestSubscriber;

    private SalesChannelContextFactory $salesChannelContextFactory;

    #[\Override]
    protected function setUp(): void
    {
        $container = $this->getContainer();

        /** @var OrderRouteRequestSubscriber $subscriber */
        $subscriber = $container->get(OrderRouteRequestSubscriber::class);
        $this->orderRouteRequestSubscriber = $subscriber;

        /** @var SalesChannelContextFactory $contextFactory */
        $contextFactory = $container->get(SalesChannelContextFactory::class);
        $this->salesChannelContextFactory = $contextFactory;
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                OrderRouteRequestEvent::class => 'onOrderRouteRequest',
            ],
            OrderRouteRequestSubscriber::getSubscribedEvents()
        );
    }

    public function testOnOrderRouteRequest(): void
    {
        $salesChannelContext = $this->salesChannelContextFactory->create(Uuid::randomHex(), TestDefaults::SALES_CHANNEL);
        $event = new OrderRouteRequestEvent(new Request(), new Request(), $salesChannelContext);

        $this->orderRouteRequestSubscriber->onOrderRouteRequest($event);

        $criteria = $event->getCriteria();
        $associations = $criteria->getAssociations();
        self::assertArrayHasKey('sptecOrderComments', $associations);

        $sptecOrderCommentsAssociation = $associations['sptecOrderComments'];
        self::assertInstanceOf(Criteria::class, $sptecOrderCommentsAssociation);
    }
}
