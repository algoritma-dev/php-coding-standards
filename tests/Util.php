<?php

declare(strict_types=1);

namespace Algoritma\CodingStandardsTest;

class Util
{
    public static function getComposerContent(): string
    {
        return <<<'JSON'
            {
              "name": "algoritma-dev/php-coding-standard-test",
              "description": "Facile coding standard test",
              "type": "project",
              "license": "proprietary",
              "keywords": [
                "facile.it"
              ],
              "homepage": "http://www.facile.it/",
              "support": {
                "email": "thomas.vargiu@facile.it"
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
                  ],
                  "Application\\Root\\": ""
                }
              },
              "autoload-dev": {
                "psr-4": {
                  "Application\\Test\\": [
                    "tests/"
                  ]
                }
              }
            }

            JSON;
    }
}
