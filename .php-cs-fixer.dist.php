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

$excludes = [];
foreach ($autoloadPathProvider->getPaths() as $path) {
    if(file_exists($dirs = $path . '.php-cs-fixer.excl.php')) {
        foreach ($dirs as $dir) {
            $excludes[] = require($dir);
        }
    }
}

$finder->in($autoloadPathProvider->getPaths())->exclude($excludes);

$config->setFinder($finder);

return $config;
