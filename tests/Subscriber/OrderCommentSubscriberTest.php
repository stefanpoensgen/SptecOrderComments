<?php

declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\Context\SalesChannelContextFactory;
use Shopware\Core\System\User\UserCollection;
use Shopware\Core\System\User\UserEntity;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentCreatedEvent;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentUpdatedEvent;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentCollection;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentDefinition;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentEntity;
use SptecOrderComments\Subscriber\OrderCommentSubscriber;

class OrderCommentSubscriberTest extends TestCase
{
    use IntegrationTestBehaviour;

    private OrderCommentSubscriber $orderCommentSubscriber;

    private SalesChannelContextFactory $salesChannelContextFactory;

    /**
     * @var EntityRepository<UserCollection>
     */
    private EntityRepository $userRepository;

    /**
     * @var EntityRepository<OrderCollection>
     */
    private EntityRepository $orderRepository;

    /**
     * @var EntityRepository<OrderCommentCollection>
     */
    private EntityRepository $orderCommentRepository;

    #[\Override]
    protected function setUp(): void
    {
        $container = $this->getContainer();

        /** @var OrderCommentSubscriber $subscriber */
        $subscriber = $container->get(OrderCommentSubscriber::class);
        $this->orderCommentSubscriber = $subscriber;

        /** @var SalesChannelContextFactory $contextFactory */
        $contextFactory = $container->get(SalesChannelContextFactory::class);
        $this->salesChannelContextFactory = $contextFactory;

        $this->userRepository = $this->getContainer()->get('user.repository');
        $this->orderRepository = $this->getContainer()->get('order.repository');
        $this->orderCommentRepository = $this->getContainer()->get('sptec_order_comment.repository');
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [
                'sptec_order_comment.written' => 'onOrderCommentWritten',
            ],
            OrderCommentSubscriber::getSubscribedEvents()
        );
    }

    public function testCreateOnOrderCommentWritten(): void
    {
        $caughtEvent = null;
        $this->addEventListener(
            $this->getContainer()->get('event_dispatcher'),
            CheckoutOrderCommentCreatedEvent::class,
            static function (CheckoutOrderCommentCreatedEvent $event) use (&$caughtEvent): void {
                $caughtEvent = $event;
            }
        );

        $content = Uuid::randomHex();
        $writtenEvent = $this->createOrderComment($content, false);
        $event = $writtenEvent->getEventByEntityName(OrderCommentDefinition::ENTITY_NAME);
        $id = $writtenEvent->getPrimaryKeys(OrderCommentDefinition::ENTITY_NAME)[0];

        $criteria = new Criteria([$id]);
        $criteria->addAssociation('media');
        $context = new Context(new SystemSource());

        /** @var OrderCommentEntity $orderComment */
        $orderComment = $this->orderCommentRepository->search($criteria, $context)->first();

        self::assertNotNull($event);
        self::assertNotEmpty($event->getWriteResults()[0]);
        self::assertSame($content, $orderComment->getContent());
        self::assertNotNull($orderComment->getMedia());
        self::assertInstanceOf(CheckoutOrderCommentCreatedEvent::class, $caughtEvent);
    }

    public function testUpdateOnOrderCommentWritten(): void
    {
        $caughtEvent = null;
        $this->addEventListener(
            $this->getContainer()->get('event_dispatcher'),
            CheckoutOrderCommentUpdatedEvent::class,
            static function (CheckoutOrderCommentUpdatedEvent $event) use (&$caughtEvent): void {
                $caughtEvent = $event;
            }
        );

        $content = Uuid::randomHex();
        $writtenEvent = $this->updateOrderComment($content, false);
        $event = $writtenEvent->getEventByEntityName(OrderCommentDefinition::ENTITY_NAME);

        self::assertNotNull($event);
        self::assertNotEmpty($event->getWriteResults()[0]);
        self::assertSame($content, $event->getWriteResults()[0]->getPayload()['content']);
        self::assertInstanceOf(CheckoutOrderCommentUpdatedEvent::class, $caughtEvent);
    }

    private function createOrderComment(string $content, bool $internal = true): EntityWrittenContainerEvent
    {
        $context = new Context(new SystemSource());

        /** @var UserEntity $user */
        $user = $this->userRepository->search(new Criteria(), $context)->first();

        /** @var OrderEntity $order */
        $order = $this->orderRepository->search(new Criteria(), $context)->first();

        $mediaId = Uuid::randomHex();

        return $this->orderCommentRepository->create([
            [
                'orderId' => $order->getId(),
                'content' => $content,
                'internal' => $internal,
                'createdById' => $user->getId(),
                'media' => [
                    [
                        'id' => $mediaId,
                        'media' => [
                            'id' => $mediaId,
                            'name' => 'test file',
                        ],
                    ],
                ],
            ],
        ], $context);
    }

    private function updateOrderComment(string $content, bool $internal = true): EntityWrittenContainerEvent
    {
        $context = new Context(new SystemSource());

        $writtenEvent = $this->createOrderComment('updateMe', $internal);
        $id = $writtenEvent->getPrimaryKeys(OrderCommentDefinition::ENTITY_NAME)[0];

        return $this->orderCommentRepository->update([
            [
                'id' => $id,
                'content' => $content,
                'internal' => $internal,
            ],
        ], $context);
    }
}
