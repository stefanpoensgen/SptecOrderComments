<?php

declare(strict_types=1);

use Shopware\Core\DevOps\StaticAnalyze\StaticAnalyzeKernel;
use Shopware\Core\Framework\Plugin\KernelPluginLoader\StaticKernelPluginLoader;
use Shopware\Core\Kernel;
use SptecOrderComments\SptecOrderComments;
use Symfony\Component\Dotenv\Dotenv;

$projectRoot = dirname(__DIR__, 4);
$pluginRootPath = dirname(__DIR__);

$classLoader = require $projectRoot . '/vendor/autoload.php';
if (file_exists($projectRoot . '/.env')) {
    (new Dotenv())->usePutEnv()->load($projectRoot . '/.env');
}

$composerJson = json_decode(
    (string) file_get_contents($pluginRootPath . '/composer.json'),
    true,
    512,
    \JSON_THROW_ON_ERROR
);

$sptecOrderComments = [
    'autoload' => $composerJson['autoload'],
    'baseClass' => SptecOrderComments::class,
    'managedByComposer' => true,
    'name' => 'PokoCore',
    'version' => $composerJson['version'],
    'active' => true,
    'path' => $pluginRootPath,
];

$pluginLoader = new StaticKernelPluginLoader($classLoader, null, [$sptecOrderComments]);

if (class_exists(Kernel::class)) {
    $kernel = new Kernel('dev', true, $pluginLoader, 'phpstan-test-cache-id');
} else {
    $kernel = new StaticAnalyzeKernel('dev', true, $pluginLoader, 'phpstan-test-cache-id');
}
$kernel->boot();

$phpStanConfigDist = file_get_contents($pluginRootPath . '/phpstan.neon.dist');
if ($phpStanConfigDist === false) {
    throw new RuntimeException('phpstan.neon.dist file not found');
}

// because the cache dir is hashed by Shopware, we need to set the PHPStan config dynamically
$phpStanConfig = str_replace(
    [
        '%ShopwareHashedCacheDir%',
        '%ShopwareRoot%',
        '%ShopwareKernelClass%',
    ],
    [
        str_replace($kernel->getProjectDir(), '', $kernel->getCacheDir()),
        $projectRoot . (is_dir($projectRoot . '/platform') ? '/platform' : ''),
        str_replace('\\', '_', get_class($kernel)),
    ],
    $phpStanConfigDist
);

file_put_contents(__DIR__ . '/../phpstan.neon', $phpStanConfig);
