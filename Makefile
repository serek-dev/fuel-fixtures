start: stop build tests

tests: _unit _stan

_unit:
	docker-compose run --rm phpunit

_stan:
	composer phpstan

build:
	docker-compose pull
	docker-compose build --pull
	composer install --ignore-platform-reqs

stop:
	docker-compose down -v --remove-orphans
	docker network prune -f
