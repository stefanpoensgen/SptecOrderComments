<?php declare(strict_types=1);

namespace SptecOrderComments\Test;

use PHPUnit\Framework\TestCase;
use Shopware\Core\Content\Media\Event\MediaFileExtensionWhitelistEvent;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use SptecOrderComments\Subscriber\MediaFileExtensionWhitelistSubscriber;

class MediaFileExtensionWhitelistSubscriberTest extends TestCase
{
    use IntegrationTestBehaviour;

    /**
     * @var MediaFileExtensionWhitelistSubscriber
     */
    private $mediaFileExtensionWhitelistSubscriber;

    /**
     * @var SystemConfigService
     */
    private $systemConfigService;

    protected function setUp(): void
    {
        $container = $this->getContainer();

        /** @var MediaFileExtensionWhitelistSubscriber $subscriber */
        $subscriber = $container->get(MediaFileExtensionWhitelistSubscriber::class);
        $this->mediaFileExtensionWhitelistSubscriber = $subscriber;

        /** @var SystemConfigService $systemConfigService */
        $systemConfigService = $container->get(SystemConfigService::class);
        $this->systemConfigService = $systemConfigService;
    }

    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            [
                MediaFileExtensionWhitelistEvent::class => 'addEntriesToFileExtensionWhitelist',
            ],
            MediaFileExtensionWhitelistSubscriber::getSubscribedEvents()
        );
    }

    public function testAddEntriesToFileExtensionWhitelist(): void
    {
        $this->systemConfigService->set('SptecOrderComments.config.extensionWhitelist', 'docx, xlsx');
        $event = new MediaFileExtensionWhitelistEvent([]);

        $this->mediaFileExtensionWhitelistSubscriber->addEntriesToFileExtensionWhitelist($event);

        static::assertContains('docx', $event->getWhitelist());
        static::assertContains('xlsx', $event->getWhitelist());
    }
}
