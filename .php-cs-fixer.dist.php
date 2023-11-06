<?php

$providers = [
    new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
    new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),
];

$rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider($providers);

$config = new PhpCsFixer\Config('algoritma/php-coding-standard');
$config->setRules($rulesProvider->getRules());

$config->setUsingCache(false);
$config->setRiskyAllowed(true);

$finder = new PhpCsFixer\Finder();
$autoloadPathProvider = new Algoritma\CodingStandards\AutoloadPathProvider();

$finder
    ->in($autoloadPathProvider->getPaths())
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config->setFinder($finder);

return $config;
