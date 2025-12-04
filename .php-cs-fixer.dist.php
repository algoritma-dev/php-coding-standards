<?php

use Algoritma\CodingStandards\Shared\Rules\CompositeRulesProvider;
use Algoritma\CodingStandards\PhpCsFixer\Rules\DefaultRulesProvider;
use Algoritma\CodingStandards\Shared\Rules\ArrayRulesProvider;
use PhpCsFixer\Config;
use Algoritma\CodingStandards\AutoloadPathProvider;
use PhpCsFixer\Finder;

$additionalRules = [
    'declare_strict_types' => true,
    'php_unit_construct' => true,
    'php_unit_dedicate_assert' => true,
    'phpdoc_to_comment' => false,
    'random_api_migration' => true,
    'self_accessor' => true,
];
$rulesProvider = new CompositeRulesProvider([
    new DefaultRulesProvider(),
    new ArrayRulesProvider($additionalRules),
]);

$config = new Config();
$config->setRules($rulesProvider->getRules());

$config->setUsingCache(true);
$config->setRiskyAllowed(true);
$config->setUnsupportedPhpVersionAllowed(true);

$autoloadPathProvider = new AutoloadPathProvider();

$finder = new Finder();
$finder->in($autoloadPathProvider->getPaths());
$config->setFinder($finder);

return $config;
