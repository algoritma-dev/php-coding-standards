# Algoritma.it PHP Coding Standard

## Installation

Currently, [Composer](https://getcomposer.org/) is the only supported installation tool.

```
$ composer require --dev algoritma/php-coding-standard
```

When you install it, a plugin will ask you some questions to setup your project automatically.

The installer will add a `.php-cs-fixer.dist.php` file in your project root directory,
then you can edit manually if you need some changes.

The CS config will be configured to find your project files using
composer autoload sources.

Only `psr-0`, `psr-4` and `classmap` autoloads are supported.

The installer will also add two scripts in your `composer.json`;

```php
"scripts": {
  "cs-check": "php-cs-fixer fix --dry-run --diff",
  "cs-fix": "php-cs-fixer fix --diff"
}
```

## Configuration

The installation configuration should be enough to use it.

If you need to change the CS config file, we suggest to don't edit the main `.php-cs-fixer.dist.php` file.

You can create a new file `.php-cs-fixer.php` with something like this:

```php
<?php

/** @var PhpCsFixer\Config $config */
$config = require __DIR__ . '/.php-cs-fixer.dist.php';

// change your configuration...
$config->setUsingCache(false);

return $config;
```

## Usage

To start code style check:

```
$ composer cs-check
```

To automatically fix code style:

```
$ composer cs-fix
```

## PhpCsFixer configuration

See [PhpCsFixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) GitHub page.

## Risky rules

Risky rules may be unstable, and cause unintended behavioral changes to your code. If you want to add these rules, you can create your own `.php-cs-fixer.php`
configuration:

```php
<?php

/** @var \PhpCsFixer\Config $config */
$config = include __DIR__ . '/.php-cs-fixer.dist.php';

$rulesProvider = new Algoritma\CodingStandards\Rules\CompositeRulesProvider([
    new Algoritma\CodingStandards\Rules\DefaultRulesProvider(),
    new Algoritma\CodingStandards\Rules\RiskyRulesProvider(),
    new Algoritma\CodingStandards\Rules\ArrayRulesProvider([
        // additional rules or rules to override
    ]),
]);

$config->setRules($rulesProvider->getRules());

return $config;

```

## Generate configuration

If you have any problem updating to a new version, you can regenerate
the default `.php-cs-fixer.dist.php` with the command:

```
$ composer algoritma-cs-create-config
```

```
$ composer algoritma-cs-create-config --help

Usage:
  algoritma-cs-create-config [options]

Options:
      --no-dev                   Do not include autoload-dev directories
      --no-risky                 Do not include risky rules
```
