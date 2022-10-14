<?php declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Uuid\Uuid;
use SptecOrderComments\Extension\Checkout\Order\OrderComment\OrderCommentCollection;

class OrderCommentTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $orderRepository;

    private EntityRepository $orderCommentRepository;

    private EntityRepository $userRepository;

    public function setUp(): void
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
        static::assertSame($orderCommentId, $orderCommentIds->firstId());
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

        static::assertTrue($orderComments->has($orderCommentId));
    }

    public function testDeleteOrderComment(): void
    {
        $context = new Context(new SystemSource());
        $orderCommentId = Uuid::randomHex();
        $this->createOrderComment($orderCommentId);

        $orderCommentIds = $this->orderCommentRepository->searchIds(new Criteria([$orderCommentId]), $context);

        static::assertSame($orderCommentId, $orderCommentIds->firstId());

        $this->orderCommentRepository->delete([['id' => $orderCommentIds->firstId()]], $context);

        $orderComment = $this->orderCommentRepository->search(new Criteria([$orderCommentId]), $context)->first();
        static::assertNull($orderComment);
    }

    private function createOrderComment(string $orderCommentId): string
    {
        $context = new Context(new SystemSource());
        $orderId = $this->orderRepository->searchIds(new Criteria(), $context)->firstId();
        $userId = $this->userRepository->searchIds(new Criteria(), $context)->firstId();

        static::assertNotNull($orderId);
        static::assertNotNull($userId);

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
}
