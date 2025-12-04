# Algoritma PHP Coding Standard

| Version | PHP version supported |
|-------- |-----------------------|
| 3.0     | >= 8.1                |

## Installation

To install the coding standard, run the following command:

```
$ composer require --dev algoritma/php-coding-standards
```

When you install it, a plugin will ask you some questions to setup your project automatically.

The installer will add the `.php-cs-fixer.dist.php`, `rector.php`, and `phpstan.neon` files in your project root directory. You can then edit them manually if you need to make any changes.

The CS config will be configured to find your project files using composer autoload sources. Only `psr-0`, `psr-4` and `classmap` autoloads are supported.

The installer will also add five scripts in your `composer.json`;

```json
"scripts": {
    "cs-check": "php-cs-fixer fix --dry-run --diff --allow-risky yes",
    "cs-fix": "php-cs-fixer fix --diff --allow-risky yes",
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

## Main Dependencies

This project relies on the following tools to enforce coding standards:

*   [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer)
*   [Rector](https://github.com/rectorphp/rector)
*   [PHPStan](https://github.com/phpstan/phpstan)

## Configuration

For more information on how to configure the tools, please refer to their official documentation.

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
