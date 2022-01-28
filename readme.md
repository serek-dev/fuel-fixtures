# Fixtures
This package is a simple util for generating commonly used data fixtures.

## Basic usage

Documentation will be fulfilled. Consider the following Fixture example for Currency model:

```php
<?php

namespace Tests\Fixtures;

use Model_Orm_Currency;
use Stwarog\FuelFixtures\Fuel\Factory;

final class CurrencyFixture extends Factory
{
    # Constants representing the "state" name
    public const USD = 'usd';
    public const EUR = 'eur';
    public const PLN = 'pln';

    
    /** @return array<string, string> */
    public function getDefaults(): array
    {
        # This values must be as random as possible
        return [
            'code' => $this->faker->countryISOAlpha3,
            'rate' => $this->faker->randomFloat(1, 1, 10),
        ];
    }

    # Class name for wchich we are going to create a new instance
    public static function getClass(): string
    {
        return Model_Orm_Currency::class;
    }

    /** array<string, callable> */
    public function getStates(): array
    {
        # Main method, returning list of available states (used by calling "with" method)
        return [
            self::USD => function (Model_Orm_Currency $model, array $attributes = []) {
                $model->code = 'USD';
                $model->rate = 1.0;
            },
            self::EUR => function (Model_Orm_Currency $model, array $attributes = []) {
                $model->code = 'EUR';
                $model->rate = 0.8418;
            },
            self::PLN => function (Model_Orm_Currency $model, array $attributes = []) {
                $model->code = 'PLN';
                $model->rate = 3.5896;
            },
        ];
    }
}
```

Each of factory provides few very important methods (like Laravel`s one):

```php
    /**
     * @param array<string, mixed> $attributes
     * @return Model<array>
     */
    public function makeOne(array $attributes = []): Model;

    /**
     * @param array<string, mixed> $attributes
     * @return array<Model>
     */
    public function makeMany(array $attributes = [], int $count = 5): array;

    /**
     * @param array<string, mixed> $attributes
     * @return Model<array>
     */
    public function createOne(array $attributes = []): Model;

    /**
     * @param array<string, mixed> $attributes
     * @return array<Model>
     */
    public function createMany(array $attributes = [], int $count = 5): array;
```

`MakeOne/Many` - creates new instance.

`CreateOne/Many` - creates new instance and persists using provided persistence strategy.

Sample call using `with`:

```php 
    $fixture = CurrencyFixture::initialize();
    # All states will be executed in the consecutive order
    $fixture->with('usd', 'rate1000', fn(Model $m, array $attrs = []) => $m->rate = 1.5)->makeOne();
```

## Development

### Standards
This package follows PSR-4 for autoloading and PSR-12 for styling.

### Useful commands
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

### Events

There is an abstraction of event dispatcher [PSR-14](https://www.php-fig.org/psr/psr-14/) with NullObject implementation by default.

The intention is to add a capability to modify prepared model data in concrete situations, from the outside.

You can initialize concrete dispatcher by dependency and then access predefined events:

| Name                   | Class           | Description                                                                                         |
|------------------------|-----------------|-----------------------------------------------------------------------------------------------------|
| model.before.prepared  | BeforePrepared  | Called right before any states (closures) has been applied.                                         |
| model.after.prepared   | AfterPrepared   | Called right after all states (closures) has been applied and before persistence event.             |
| model.before.persisted | BeforePersisted | Called right before prepared model is persisted in DB e.g. classes UowPersistence, FuelPersistence. |
| model.after.persisted  | AfterPersisted  | Called right after prepared model is persisted in DB.                                               |

## Change Log

2.0.0 (2021-01-28)

- upgrade to PHP8 + uow fuel v2.0 usage

1.2.0 (2021-12-02) **BREAKING CHANGE**

- Refactor - added Config for Factory Dependency as amount of it grows up to quickly
- Added PSR container dependency
- Fixed Reference type (in getStates), to be aware of Container and avoid re-creating new instance if entry is present
  as DI

1.1.0 (2021-12-02)

- Added event dispatcher abstraction, and events: BeforePersisted, ModelPrepared
