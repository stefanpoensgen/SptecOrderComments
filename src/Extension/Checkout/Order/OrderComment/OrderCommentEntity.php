<?php

declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\User\UserEntity;

class OrderCommentEntity extends Entity
{
    use EntityIdTrait;

    protected string $orderId;

    protected ?OrderEntity $order = null;

    protected string $createdById;

    protected ?UserEntity $createdBy = null;

    protected string $content;

    protected bool $internal = true;

    protected ?bool $task = null;

    protected ?OrderCommentMediaCollection $media = null;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function getOrder(): ?OrderEntity
    {
        return $this->order;
    }

    public function setOrder(?OrderEntity $order): void
    {
        $this->order = $order;
    }

    public function getCreatedById(): string
    {
        return $this->createdById;
    }

    public function setCreatedById(string $createdById): void
    {
        $this->createdById = $createdById;
    }

    public function getCreatedBy(): ?UserEntity
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?UserEntity $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getInternal(): bool
    {
        return $this->internal;
    }

    public function setInternal(bool $internal): void
    {
        $this->internal = $internal;
    }

    public function getTask(): ?bool
    {
        return $this->task;
    }

    public function setTask(?bool $task): void
    {
        $this->task = $task;
    }

    public function getMedia(): ?OrderCommentMediaCollection
    {
        return $this->media;
    }

    public function setMedia(OrderCommentMediaCollection $media): void
    {
        $this->media = $media;
    }
}
