# Makefile for Insight Core Project
setup: build composer-update

PHP_V=8.1

DOCKER_COMPOSE_RUN=docker-compose run --rm php${PHP_V}

shell:
	${DOCKER_COMPOSE_RUN} sh

start:
	docker-compose up -d php${PHP_V}

composer-update:
	${DOCKER_COMPOSE_RUN} php composer.phar update

pre-commit-check: rector cs-fix phpstan tests

rector:
	${DOCKER_COMPOSE_RUN} vendor/bin/rector --ansi

cs-fix:
	${DOCKER_COMPOSE_RUN} vendor/bin/php-cs-fixer fix --verbose --ansi

phpstan:
	${DOCKER_COMPOSE_RUN} vendor/bin/phpstan analyse --ansi --memory-limit=-1 --configuration phpstan${PHP_V}.neon

test:
	${DOCKER_COMPOSE_RUN} vendor/bin/phpunit --colors=always --display-warnings --display-deprecations
