# Makefile for Insight Core Project
setup: build composer-update

shell:
	docker-compose run --user 1000:1000 --rm php bash

start:
	docker-compose up -d php

composer-update: start
	docker-compose exec --user 1000:1000 php composer update

pre-commit-check: rector cs-fix phpstan tests

rector: start
	docker-compose exec --user 1000:1000 php vendor/bin/rector --ansi

cs-fix: start
	docker-compose exec --user 1000:1000 php vendor/bin/php-cs-fixer fix --verbose --ansi

phpstan: start
	docker-compose exec --user 1000:1000 php vendor/bin/phpstan analyse --ansi --memory-limit=-1

tests: start
	docker-compose exec --user 1000:1000 php vendor/bin/phpunit --colors=always
