<?php

declare(strict_types=1);

namespace SptecOrderComments\Subscriber;

use Shopware\Core\Content\Media\Event\MediaFileExtensionWhitelistEvent;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class MediaFileExtensionWhitelistSubscriber implements EventSubscriberInterface
{
    public function __construct(private SystemConfigService $systemConfigService) {}

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            MediaFileExtensionWhitelistEvent::class => 'addEntriesToFileExtensionWhitelist',
        ];
    }

    public function addEntriesToFileExtensionWhitelist(MediaFileExtensionWhitelistEvent $event): void
    {
        $extensionWhitelist = $this->systemConfigService->get('SptecOrderComments.config.extensionWhitelist');
        if (!\is_string($extensionWhitelist)) {
            return;
        }

        $extensionWhitelist = \array_map('trim', \explode(',', $extensionWhitelist));
        $whiteList = $event->getWhitelist();
        $event->setWhitelist(\array_merge($whiteList, $extensionWhitelist));
    }
}
