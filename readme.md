# Fixtures
This package is a simple util for generating commonly used data fixtures.

## Important design decisions

### Proxy class
Default Fuel's Model class is wrapped by proxy class to prevent weird behaviour of this package
as unexpected DB calls (package allows to create fixtures in isolation or not).

## Development
The whole project is Unit tested and protected with strong static code analytics (phpstan).
```bash
php composer tests:unit # for unit testing
```
```bash
php composer phpstan # for phpstan validation
```

Code is dockerized and simplified by makefile. Simply run:
```bash
make # to execute all mandatory quality check commands
```
