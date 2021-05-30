# Fixtures
This package is a simple util for generating commonly used data fixtures.

## Development
The whole project is Unit tested and protected with strong static code analytics (phpstan).
```bash
make unit # for unit testing
```

```bash
make phpstan # for phpstan validation
```

```bash
make cs     # for phpcs validation
make cs_fix # for phpcbf auto fix attempt
```

Code is dockerized and simplified by makefile. Simply run:

```bash
make # to execute all mandatory quality check commands
```

**If you can't run make file locally, then checkout the direct commands in composer.json.**
