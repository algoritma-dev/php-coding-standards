<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest;

class Util
{
    public static function getComposerContent(): string
    {
        return <<<'JSON'
{
  "name": "algoritma/php-coding-standard-test",
  "description": "Algoritma coding standard test",
  "type": "project",
  "license": "proprietary",
  "keywords": [
    "algoritma.it"
  ],
  "homepage": "https://www.algoritma.it/",
  "support": {
    "email": "raffaele.carelle@algoritma.it"
  },
  "config": {
    "sort-packages": true
  },
  "require": {
    "php": "^7.0",
    "roave/security-advisories": "dev-master"
  },
  "require-dev": {
    "phpunit/phpunit": "^6.0"
  },
  "autoload": {
    "psr-4": {
      "Application\\": [
        "src/"
      ]
    }
  },
  "autoload-dev": {
    "psr-4": {
      "ApplicationTest\\": [
        "tests/"
      ]
    }
  }
}

JSON;
    }
}
