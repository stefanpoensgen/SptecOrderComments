<?php declare(strict_types=1);

namespace SptecOrderComments\Subscriber;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityWriteResult;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\EntityNotFoundException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentCreatedEvent;
use SptecOrderComments\Core\Checkout\Order\Event\CheckoutOrderCommentUpdatedEvent;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentDefinition;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentEntity;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderCommentSubscriber implements EventSubscriberInterface
{
    private EventDispatcherInterface $eventDispatcher;

    private EntityRepository $orderCommentRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityRepository $orderCommentRepository
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->orderCommentRepository = $orderCommentRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            'sptec_order_comment.written' => 'onOrderCommentWritten',
        ];
    }

    public function onOrderCommentWritten(EntityWrittenEvent $event): void
    {
        $context = $event->getContext();
        if ($event->hasErrors() === true || $context->getVersionId() !== Defaults::LIVE_VERSION) {
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
            if ($order === null) {
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

        $orderComment = $this->orderCommentRepository
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
