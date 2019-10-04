.DOCKER_RUN_PHP:= docker-compose run --rm mappics
.RUN=

ifneq ($(AM_I_INSIDE_DOCKER),true)
    .RUN := $(.DOCKER_RUN_PHP)
endif

.PHONY: build up down down-with-volumes unit-test integration-test phpstan cs-fixer

setup: build dependencies migrations
start: up
stop: down
destroy: down-with-volumes
restart: stop start
test: dependencies cache-test migrations-test phpunit
coverage: dependencies cache-test migrations-test phpunit-coverage
qa-suite: dependencies cache-test migrations-test phpstan phpunit infection

build:
	docker-compose build --no-cache

up:
	docker-compose up -d

down:
	docker-compose down

down-with-volumes:
	docker-compose down --remove-orphans --volumes

dependencies:
	$(.RUN) composer install

migrations:
	$(.RUN) bin/console doctrine:migrations:migrate --env=dev --no-interaction

migrations-test:
	$(.RUN) bin/console doctrine:migrations:migrate --env=test --no-interaction

cache:
	$(.RUN) bin/console cache:clear --env=dev

cache-test:
	$(.RUN) bin/console cache:clear --env=test

phpunit:
	$(.RUN) vendor/bin/phpunit

phpunit-coverage:
	$(.RUN) vendor/bin/phpunit --coverage-html=var/coverage

phpstan:
	$(.RUN) vendor/bin/phpstan analyse --level=max src/

infection:
	$(.RUN) vendor/bin/infection --threads=4

shell:
	$(.DOCKER_RUN_PHP) bash
