<?php

declare(strict_types=1);

namespace SptecOrderComments\Extension\Checkout\Order\OrderComment;

use Shopware\Core\Checkout\Order\OrderDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class OrderCommentExtension extends EntityExtension
{
    #[\Override]
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (
            new OneToManyAssociationField(
                OrderCommentDefinition::EXTENSION_NAME,
                OrderCommentDefinition::class,
                'order_id'
            ))->addFlags(new ApiAware(), new CascadeDelete()),
        );
    }

    #[\Override]
    public function getDefinitionClass(): string
    {
        return OrderDefinition::class;
    }
}
