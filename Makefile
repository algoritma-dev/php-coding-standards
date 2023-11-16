.PHONY: pre-commit-check

cs:
	docker run --rm -v $(PWD):/code -w /code docker.algoritma.it/algoritma/php:8.2-cli-alpine3.16 vendor/bin/php-cs-fixer fix --verbose

cs-dry-run:
	docker run --rm -v $(PWD):/code -w /code docker.algoritma.it/algoritma/php:8.2-cli-alpine3.16 vendor/bin/php-cs-fixer fix --verbose --dry-run

psalm:
	docker run --rm -v $(PWD):/code -w /code docker.algoritma.it/algoritma/php:8.2-cli-alpine3.16 vendor/bin/psalm -m

test:
	docker run --rm -v $(PWD):/code -w /code docker.algoritma.it/algoritma/php:8.2-cli-alpine3.16 vendor/bin/phpunit

pre-commit-check: cs psalm test
