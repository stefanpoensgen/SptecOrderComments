<?php

declare(strict_types=1);

namespace SptecOrderComments\Subscriber;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\EntityNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentCreatedEvent;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentUpdatedEvent;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentCollection;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentDefinition;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class OrderCommentSubscriber implements EventSubscriberInterface
{
    /**
     * @param EntityRepository<OrderCommentCollection> $sptecOrderCommentRepository
     */
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private EntityRepository $sptecOrderCommentRepository
    ) {}

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'sptec_order_comment.written' => 'onOrderCommentWritten',
        ];
    }

    public function onOrderCommentWritten(EntityWrittenEvent $event): void
    {
        $context = $event->getContext();
        if ($event->hasErrors() || $context->getVersionId() !== Defaults::LIVE_VERSION) {
            return;
        }

        foreach ($event->getWriteResults() as $result) {
            $payload = $result->getPayload();
            $id = $payload['id'] ?? null;

            if ($id === null) {
                continue;
            }

            $orderComment = $this->getOrderComment($id, $context);
            if ($orderComment->getInternal()) {
                continue;
            }

            $order = $orderComment->getOrder();
            if (!$order instanceof OrderEntity) {
                continue;
            }

            switch ($result->getOperation()) {
                case EntityWriteResult::OPERATION_INSERT:
                    $this->eventDispatcher->dispatch(
                        new CheckoutOrderCommentCreatedEvent($order, $orderComment, $context)
                    );

                    break;
                case EntityWriteResult::OPERATION_UPDATE:
                    $this->eventDispatcher->dispatch(
                        new CheckoutOrderCommentUpdatedEvent($order, $orderComment, $context)
                    );

                    break;
                default:
                    break;
            }
        }
    }

    private function getOrderComment(string $id, Context $context): OrderCommentEntity
    {
        $criteria = $this->getCommentOrderCriteria($id);

        $orderComment = $this->sptecOrderCommentRepository
            ->search($criteria, $context)
            ->first();

        if (!$orderComment instanceof OrderCommentEntity) {
            throw new EntityNotFoundException(OrderCommentDefinition::ENTITY_NAME, $id);
        }

        return $orderComment;
    }

    private function getCommentOrderCriteria(string $id): Criteria
    {
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('order');
        $criteria->addAssociation('order.orderCustomer.salutation');
        $criteria->addAssociation('order.orderCustomer.customer');
        $criteria->addAssociation('order.stateMachineState');
        $criteria->addAssociation('order.deliveries.shippingMethod');
        $criteria->addAssociation('order.deliveries.shippingOrderAddress.country');
        $criteria->addAssociation('order.deliveries.shippingOrderAddress.countryState');
        $criteria->addAssociation('order.salesChannel');
        $criteria->addAssociation('order.language.locale');
        $criteria->addAssociation('order.transactions.paymentMethod');
        $criteria->addAssociation('order.lineItems');
        $criteria->addAssociation('order.currency');
        $criteria->addAssociation('order.addresses.country');
        $criteria->addAssociation('order.addresses.countryState');

        return $criteria;
    }
}
