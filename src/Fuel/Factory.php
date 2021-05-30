<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Faker\Generator;
use Orm\Model;

abstract class Factory implements FactoryContract
{
    protected PersistenceContract $persistence;
    protected ?Generator $faker;

    public function __construct(?PersistenceContract $persistence = null, ?Generator $faker = null)
    {
        $this->persistence = $persistence ?? new FuelPersistence();
        $this->faker = $faker ?? \Faker\Factory::create();
    }

    /**
     * @return static|self
     */
    public static function initialize(?PersistenceContract $persistence = null): self
    {
        return new static($persistence);
    }

    /** @inheritDoc */
    public function makeOne(array $attributes = []): Model
    {
        $attributes = array_merge($this->getDefaults(), $attributes);
        $class = static::getClass();

        return new $class($attributes);
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
    public function with(string $state): FactoryContract
    {
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
}
