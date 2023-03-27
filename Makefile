vendors:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) composer/composer:latest composer install

vendor-req:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) composer/composer:latest composer req $(filter-out $@,$(MAKECMDGOALS))

vendor-remove:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) composer/composer:latest composer remove $(filter-out $@,$(MAKECMDGOALS))

test:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) --rm docker.algoritma.it/algoritma/php:8.1-alpine3.16 vendor/bin/phpunit tests

install:
	docker run --workdir=$(PWD) -v $(PWD):$(PWD) composer/composer:latest composer req $(filter-out $@,$(MAKECMDGOALS))
