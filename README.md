# Algoritma PHP Coding Standard

| Version | PHP version supported |
|-------- |-----------------------|
| 3.0     | 8.1, 8.2, 8.3, 8.4    |

## Installation

Currently, [Composer](https://getcomposer.org/) is the only supported installation tool.

Add repository source on composer.json:

```
"repositories": {
    "algoritma-php-cs": {
        "type": "vcs",
        "url": "https://phpcs:glpat-Hw9asjC2Q3gX4Cy9PB9k@gitlab.algoritma.it/algoritma/php-coding-standard.git"
    }
}
```

then run the command

```
$ composer require --dev algoritma/php-coding-standard
```

When you install it, a plugin will ask you some questions to setup your project automatically.

The installer will add the `.php-cs-fixer.dist.php`, `rector.php`, `phpstan.neon` files in your project root directory,
then you can edit manually if you need some changes.

The CS config will be configured to find your project files using
composer autoload sources.

Only `psr-0`, `psr-4` and `classmap` autoloads are supported.

The installer will also add five scripts in your `composer.json`;

```php
"scripts": {
    "cs-check": "php-cs-fixer fix --dry-run --diff",
    "cs-fix": "php-cs-fixer fix --diff",
    "rector-check": "rector process --dry-run",
    "rector-fix": "rector process",
    "phpstan": "phpstan analyze"
}
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

To automatically check for refactor:

```
$ composer rector-check
```

To automatically refactor fix:

```
$ composer rector-fix
```

To run static analisys check:

```
$ composer phpstan
```

## Configuration

See [PhpCsFixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) GitHub page.

See [Rector](https://github.com/rectorphp/rector) GitHub page.

See [PHPStan](https://github.com/phpstan/phpstan) GitHub page.

## Risky rules (PHP-CS-Fixer)

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

If you have any problem updating to a new version, you can regenerate
the default `rector.php` with the command:

```
$ composer algoritma-rector-create-config
```

```
$ composer algoritma-rector-create-config --help

Usage:
  algoritma-rector-create-config [options]

Options:
      --no-dev                   Do not include autoload-dev directories
```

If you have any problem updating to a new version, you can regenerate
the default `phpstan.neon` with the command:

```
$ composer algoritma-phpstan-create-config
```

```
$ composer algoritma-phpstan-create-config --help

Usage:
  algoritma-phpstan-create-config [options]

Options:
      --no-dev                   Do not include autoload-dev directories
```
