<?php

$parameters = [
    'level' => 6,
    'fileExtensions' => ['php']
];

$includes = [];

$symfony = getenv('SYMFONY');
if ($symfony) {
    $includes = array_merge($includes, [
        '/tools/vendor/phpstan/phpstan-symfony/extension.neon',
        '/tools/vendor/phpstan/phpstan-symfony/rules.neon'
    ]);

    $containerXmlPath = getenv('SYMFONY_CONTAINER_XML_PATH');

    $parameters['symfony'] = [
        'containerXmlPath' => $containerXmlPath
    ];
}

$doctrine = getenv('DOCTRINE');

if ($doctrine) {
    $includes = array_merge($includes, [
        '/tools/vendor/phpstan/phpstan-doctrine/extension.neon',
        '/tools/vendor/phpstan/phpstan-doctrine/rules.neon'
    ]);
}

$magento = getenv('MAGENTO');

if ($magento) {
    $includes = array_merge($includes, [
        '/tools/vendor/bitexpert/phpstan-magento/extension.neon'
    ]);
}

$laravel = getenv('LARAVEL');

if ($laravel) {
    $includes = array_merge($includes, [
        '/tools/vendor/nunomaduro/larastan/extension.neon'
    ]);
}

return [
    'includes' => $includes,
    'parameters' => $parameters
];
