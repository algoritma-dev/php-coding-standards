<?php

$providers = [
    new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
    new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),
    // TODO: drop when PHP 8.0+ is required
    new Algoritma\CodingStandards\Rules\ArrayRulesProvider([
        'get_class_to_class_keyword' => false,
    ]),
];

$rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider($providers);

$config = new PhpCsFixer\Config('algoritma/php-coding-standard');
$config->setRules($rulesProvider->getRules());

$config->setUsingCache(false);
$config->setRiskyAllowed(true);

$finder = new PhpCsFixer\Finder();
$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();

$finder->in($autoloadPathProvider->getPaths());
$finder->append([
    __DIR__ . '/.php-cs-fixer.dist.php'
]);
$config->setFinder($finder);

$config->setFinder($finder);

return $config;
