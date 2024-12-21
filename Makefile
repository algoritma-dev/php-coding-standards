# Makefile for Insight Core Project
setup: build composer-update

PHP_V=7.4

shell:
	docker-compose run --user 1000:1000 --rm php${PHP_V} sh

start:
	docker-compose up -d php${PHP_V}

composer-update: start
	docker-compose exec --user 1000:1000 php${PHP_V} php composer.phar update

pre-commit-check: rector cs-fix phpstan tests

rector: start
	docker-compose exec --user 1000:1000 php${PHP_V} vendor/bin/rector --ansi

cs-fix: start
	docker-compose exec --user 1000:1000 php${PHP_V} vendor/bin/php-cs-fixer fix --verbose --ansi

phpstan: start
	docker-compose exec --user 1000:1000 php${PHP_V} vendor/bin/phpstan analyse --ansi --memory-limit=-1

tests: start
	docker-compose exec --user 1000:1000 php${PHP_V} vendor/bin/phpunit --colors=always
