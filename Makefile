start: _build _test

_build:
	docker-compose pull
	docker-compose build --pull

_test:
	docker-compose run --rm phpunit --version

stop:
	docker-compose down -v --remove-orphans
	docker network prune -f
