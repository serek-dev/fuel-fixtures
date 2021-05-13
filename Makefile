start: stop build tests

tests: stop unit stan

unit:
	docker-compose run --rm composer tests:unit

stan:
	composer phpstan

build:
	docker-compose pull
	docker-compose build --pull
	composer install --ignore-platform-reqs

stop:
	docker-compose down -v --remove-orphans
	docker network prune -f
