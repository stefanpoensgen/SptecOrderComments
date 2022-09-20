<?php declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class OrderCommentMediaEntity extends Entity
{
    use EntityIdTrait;

    protected string $orderCommentId;

    protected string $mediaId;

    protected ?MediaEntity $media;

    protected ?OrderCommentEntity $orderComment;

    public function getOrderCommentId(): string
    {
        return $this->orderCommentId;
    }

    public function setOrderCommentId(string $orderCommentId): void
    {
        $this->orderCommentId = $orderCommentId;
    }

    public function getMediaId(): string
    {
        return $this->mediaId;
    }

    public function setMediaId(string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getOrderComment(): ?OrderCommentEntity
    {
        return $this->orderComment;
    }

    public function setOrderComment(?OrderCommentEntity $orderComment): void
    {
        $this->orderComment = $orderComment;
    }
}
