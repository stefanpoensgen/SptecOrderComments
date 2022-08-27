<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\MbStrFunctionsFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $services = $ecsConfig->services();

    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [[
            'include' => [NativeFunctionInvocationFixer::SET_ALL],
            'scope' => 'namespaced',
        ]]);

    $services->set(MbStrFunctionsFixer::class);

    $parameters = $ecsConfig->parameters();

    $parameters->set('cache_directory', __DIR__ . '/var/cache/cs_fixer');

    $parameters->set('cache_namespace', 'SptecOrderComments');

    $parameters->set('paths', [__DIR__ . '/src', __DIR__ . '/tests']);
};
