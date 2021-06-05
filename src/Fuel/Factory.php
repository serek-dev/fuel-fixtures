<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Closure;
use Countable;
use Faker\Generator;
use Orm\Model;
use Stwarog\FuelFixtures\Exceptions\OutOfBound;

abstract class Factory implements FactoryContract, Countable
{
    private const IDS_STATE_KEY = '_ids';

    protected PersistenceContract $persistence;
    protected Generator $faker;

    /** @var array<string, string> */
    private array $usedStates = [];

    /** @var array<string, mixed|Closure> */
    private array $customClosures = [];

    public function __construct(?PersistenceContract $persistence = null, ?Generator $faker = null)
    {
        $this->persistence = $persistence ?? new FuelPersistence();
        $this->faker = $faker ?? \Faker\Factory::create();
    }

    /**
     * @return static|self
     */
    public static function initialize(?PersistenceContract $persistence = null, ?Generator $faker = null): self
    {
        return new static($persistence, $faker);
    }

    public static function from(FactoryContract $factory): self
    {
        return self::initialize($factory->getPersistence(), $factory->getFaker());
    }

    /** @inheritDoc */
    public function makeOne(array $attributes = []): Model
    {
        $attributes = array_merge($this->getDefaults(), $attributes);
        $class = static::getClass();

        $model = new $class($attributes);

        $definedStates = $this->getStates();
        $customStates = $this->customClosures;
        $allStates = array_merge($definedStates, $customStates);

        // apply all closures
        foreach ($this->usedStates as $stateName) {
            $allStates[$stateName]($model, $attributes);
        }

        return $model;
    }

    /** @inheritDoc */
    public function makeMany(array $attributes = [], int $count = 5): array
    {
        return array_map(fn() => $this->makeOne($attributes), range(1, $count));
    }

    /** @inheritDoc */
    public function createOne(array $attributes = []): Model
    {
        $model = $this->makeOne($attributes);

        $this->persistence->persist($model);

        return $model;
    }

    /** @inheritDoc */
    public function createMany(array $attributes = [], int $count = 5): array
    {
        $models = $this->makeMany($attributes, $count);

        $this->persistence->persist(...$models);

        return $models;
    }

    /** @inerhitDoc */
    public function getStates(): array
    {
        return [];
    }

    /** @inerhitDoc */
    public function with(string ...$states): FactoryContract
    {
        foreach ($states as $state) {
            if (!isset($this->getStates()[$state])) {
                throw OutOfBound::create($state);
            }

            $this->usedStates[$state] = $state;
        }

        return $this;
    }

    public function setPersistence(PersistenceContract $strategy): void
    {
        $this->persistence = $strategy;
    }

    public function setFaker(Generator $faker): void
    {
        $this->faker = $faker;
    }

    public function getPersistence(): PersistenceContract
    {
        return $this->persistence;
    }

    public function getFaker(): Generator
    {
        return $this->faker;
    }

    public function count(): int
    {
        return count($this->usedStates);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return Model
     */
    public function __invoke(array $attributes = []): Model
    {
        return $this->makeOne($attributes);
    }

    public function withIdsFor(string ...$fields): self
    {
        $this->usedStates[self::IDS_STATE_KEY] = self::IDS_STATE_KEY;

        $this->customClosures[self::IDS_STATE_KEY] = function (Model $model, array $attributes = []) use ($fields) {
            foreach ($fields as $field) {
                $model->$field = $this->faker->numberBetween(1, 10000);
            }
        };
        return $this;
    }

    final public function withIds(): bool
    {
        return isset($this->usedStates[self::IDS_STATE_KEY]);
    }
}
