<?php declare(strict_types=1);

use Shopware\Core\TestBootstrapper;

return (new TestBootstrapper())
    ->setProjectDir(dirname(__DIR__, 4))
    ->setLoadEnvFile(true)
    ->setForceInstallPlugins(true)
    ->addCallingPlugin()
    ->bootstrap();
