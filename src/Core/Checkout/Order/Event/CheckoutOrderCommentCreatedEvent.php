<?php declare(strict_types=1);

namespace SptecOrderComments\Core\Checkout\Order\Event;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Flow\Exception\CustomerDeletedException;
use Shopware\Core\Content\MailTemplate\Exception\MailEventConfigurationException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\CustomerAware;
use Shopware\Core\Framework\Event\EventData\EntityType;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\OrderAware;
use Shopware\Core\Framework\Event\SalesChannelAware;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentDefinition;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentEntity;
use Symfony\Contracts\EventDispatcher\Event;

class CheckoutOrderCommentCreatedEvent extends Event implements SalesChannelAware, OrderAware, MailAware, CustomerAware
{
    public const EVENT_NAME = 'checkout.order.comment.created';

    private OrderEntity $order;

    private OrderCommentEntity $orderComment;

    private Context $context;

    public function __construct(OrderEntity $order, OrderCommentEntity $orderComment, Context $context)
    {
        $this->order = $order;
        $this->orderComment = $orderComment;
        $this->context = $context;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getOrder(): OrderEntity
    {
        return $this->order;
    }

    public function getOrderComment(): OrderCommentEntity
    {
        return $this->orderComment;
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('order', new EntityType(OrderDefinition::class))
            ->add('orderComment', new EntityType(OrderCommentDefinition::class));
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if ($this->order->getOrderCustomer() === null) {
            throw new MailEventConfigurationException('Data for mailRecipientStruct not available.', self::class);
        }

        return new MailRecipientStruct([
            $this->order->getOrderCustomer()->getEmail() => $this->order->getOrderCustomer()->getFirstName() . ' ' . $this->order->getOrderCustomer()->getLastName(),
        ]);
    }

    public function getSalesChannelId(): string
    {
        return $this->order->getSalesChannelId();
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getOrderId(): string
    {
        return $this->getOrder()->getId();
    }

    public function getCustomerId(): string
    {
        $customer = $this->getOrder()->getOrderCustomer();

        if ($customer === null || $customer->getCustomerId() === null) {
            throw new CustomerDeletedException($this->getOrderId());
        }

        return $customer->getCustomerId();
    }
}
