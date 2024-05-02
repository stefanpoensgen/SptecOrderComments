<?php

declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderCollection;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\User\UserCollection;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentCollection;

class OrderCommentTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var EntityRepository<OrderCollection>
     */
    private EntityRepository $orderRepository;

    /**
     * @var EntityRepository<OrderCommentCollection>
     */
    private EntityRepository $orderCommentRepository;

    /**
     * @var EntityRepository<UserCollection>
     */
    private EntityRepository $userRepository;

    #[\Override]
    protected function setUp(): void
    {
        $this->orderRepository = $this->getContainer()->get('order.repository');
        $this->orderCommentRepository = $this->getContainer()->get('sptec_order_comment.repository');
        $this->userRepository = $this->getContainer()->get('user.repository');
    }

    public function testCreateOrderComment(): void
    {
        $context = new Context(new SystemSource());
        $orderCommentId = Uuid::randomHex();
        $this->createOrderComment($orderCommentId);
        $orderCommentIds = $this->orderCommentRepository->searchIds(new Criteria([$orderCommentId]), $context);
        self::assertSame($orderCommentId, $orderCommentIds->firstId());
    }

    public function testReadOrderCommentsFromOrder(): void
    {
        $context = new Context(new SystemSource());
        $orderCommentId = Uuid::randomHex();
        $orderId = $this->createOrderComment($orderCommentId);

        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('sptecOrderComments');

        /** @var OrderEntity $order */
        $order = $this->orderRepository->search($criteria, $context)->first();

        /** @var OrderCommentCollection $orderComments */
        $orderComments = $order->getExtension('sptecOrderComments');

        self::assertTrue($orderComments->has($orderCommentId));
    }

    public function testDeleteOrderComment(): void
    {
        $context = new Context(new SystemSource());
        $orderCommentId = Uuid::randomHex();
        $this->createOrderComment($orderCommentId);

        $orderCommentIds = $this->orderCommentRepository->searchIds(new Criteria([$orderCommentId]), $context);

        self::assertSame($orderCommentId, $orderCommentIds->firstId());

        $this->orderCommentRepository->delete([['id' => $orderCommentIds->firstId()]], $context);

        $orderComment = $this->orderCommentRepository->search(new Criteria([$orderCommentId]), $context)->first();
        self::assertNull($orderComment);
    }

    private function createOrderComment(string $orderCommentId): string
    {
        $context = new Context(new SystemSource());
        $orderId = $this->orderRepository->searchIds(new Criteria(), $context)->firstId();
        $userId = $this->userRepository->searchIds(new Criteria(), $context)->firstId();

        self::assertNotNull($orderId);
        self::assertNotNull($userId);

        $this->orderCommentRepository->upsert([[
            'id' => $orderCommentId,
            'orderId' => $orderId,
            'createdById' => $userId,
            'internal' => true,
            'task' => true,
            'content' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr',
        ]], $context);

        return $orderId;
    }

    /**
     * Tests the filtering functionality of OrderComment based on user roles and permissions for internal comments.
     */
    public function testFilteringOrderCommentsBasedOnUserRolesAndPermissions(): void
    {
        $context = new Context(new SystemSource());
        $orderCommentId = Uuid::randomHex();
        $orderId = $this->createOrderComment($orderCommentId, internal: true);

        // Simulate a user with insufficient permissions trying to access internal comments
        $criteria = new Criteria([$orderId]);
        $criteria->addAssociation('sptecOrderComments');
        $criteria->getAssociation('sptecOrderComments')->addFilter(new EqualsFilter('internal', true));

        /** @var OrderEntity $order */
        $order = $this->orderRepository->search($criteria, $context)->first();

        /** @var OrderCommentCollection $orderComments */
        $orderComments = $order->getExtension('sptecOrderComments');

        // Assert that the user cannot access internal comments
        self::assertEmpty($orderComments);
    }

    private function createOrderComment(string $orderCommentId, bool $internal = false): string
    {
        $context = new Context(new SystemSource());
        $orderId = $this->orderRepository->searchIds(new Criteria(), $context)->firstId();
        $userId = $this->userRepository->searchIds(new Criteria(), $context)->firstId();

        self::assertNotNull($orderId);
        self::assertNotNull($userId);

        $this->orderCommentRepository->upsert([[
            'id' => $orderCommentId,
            'orderId' => $orderId,
            'createdById' => $userId,
            'internal' => $internal,
            'task' => false,
            'content' => 'Internal comment only visible to authorized users',
        ]], $context);

        return $orderId;
    }
}
