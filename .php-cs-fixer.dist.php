<?php

$additionalRules = [
    'declare_strict_types' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => true,
    'phpdoc_to_comment' => false,
    'random_api_migration' => true,
    'self_accessor' => true,
];
$rulesProvider = new \Algoritma\CodingStandards\Rules\CompositeRulesProvider([
    new \Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
    new \Algoritma\CodingStandards\Rules\ArrayRulesProvider($additionalRules),
]);

$config = new PhpCsFixer\Config();
$config->setRules($rulesProvider->getRules());

$config->setUsingCache(true);
$config->setRiskyAllowed(true);

$autoloadPathProvider = new \Algoritma\CodingStandards\AutoloadPathProvider();

$finder = new PhpCsFixer\Finder();
$finder->in($autoloadPathProvider->getPaths());
$config->setFinder($finder);

return $config;
