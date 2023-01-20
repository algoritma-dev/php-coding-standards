vendors:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) composer/composer:latest composer install

test:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) --rm docker.algoritma.it/algoritma/php:8.1-alpine3.16 vendor/bin/phpunit tests
