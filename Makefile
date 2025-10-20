# Makefile for Insight Core Project
setup: build composer-update

PHP_V=8.1

DOCKER_COMPOSE_EXEC=docker-compose exec php${PHP_V}

shell:
	${DOCKER_COMPOSE_EXEC} sh

start:
	docker-compose up -d php${PHP_V}

composer-update:
	${DOCKER_COMPOSE_EXEC} php composer.phar update

pre-commit-check: rector cs-fix phpstan tests

rector:
	${DOCKER_COMPOSE_EXEC} vendor/bin/rector --ansi

cs-fix:
	${DOCKER_COMPOSE_EXEC} vendor/bin/php-cs-fixer fix --verbose --ansi

phpstan:
	${DOCKER_COMPOSE_EXEC} vendor/bin/phpstan analyse --ansi --memory-limit=-1

test:
	${DOCKER_COMPOSE_EXEC} vendor/bin/phpunit --colors=always --display-warnings --display-deprecations
