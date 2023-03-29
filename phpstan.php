<?php

$parameters = [
    'level' => 6,
    'fileExtensions' => ['php']
];

$includes = [
    'vendor/phpstan/phpstan-deprecation-rules/rules.neon'
];

$symfony = getenv('SYMFONY');
if ($symfony) {
    $includes = array_merge($includes, [
        'vendor/phpstan/phpstan-symfony/extension.neon',
        'vendor/phpstan/phpstan-symfony/rules.neon'
    ]);

    $containerXmlPath = getenv('SYMFONY_CONTAINER_XML_PATH', true);

    if (!$containerXmlPath) {
        throw new \RuntimeException('Set SYMFONY_CONTAINER_XML_PATH environment variable on docker container ');
    }

    $parameters['symfony'] = [
        'containerXmlPath' => $containerXmlPath
    ];

    $cacheConfig = getenv('SYMFONY_CACHE_CONFIG_PATH', true);

    if ($cacheConfig) {
        $parameters['scanDirectories'] = [
            $cacheConfig
        ];
    }
}

$doctrine = getenv('DOCTRINE');

if ($doctrine) {
    $includes = array_merge($includes, [
        'vendor/phpstan/phpstan-doctrine/extension.neon',
        'vendor/phpstan/phpstan-doctrine/rules.neon'
    ]);
}

$magento = getenv('MAGENTO');

if ($magento) {
    $includes = array_merge($includes, [
        'vendor/bitexpert/phpstan-magento/extension.neon'
    ]);
}

$laravel = getenv('LARAVEL');

if ($laravel) {
    $includes = array_merge($includes, [
        'vendor/nunomaduro/larastan/extension.neon'
    ]);
}

return [
    'includes' => $includes,
    'parameters' => $parameters
];
