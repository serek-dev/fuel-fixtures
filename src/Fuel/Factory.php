<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Orm\Model;

abstract class Factory implements FactoryContract
{
    private string $class;

    protected PersistenceContract $persistence;

    public function __construct(string $class, ?PersistenceContract $persistence = null)
    {
        $this->class = $class ?? $this->getClass();
        $this->persistence = $persistence ?? new FuelPersistence();
    }

    /**
     * @return static|self
     */
    public static function initialize(?PersistenceContract $persistence = null): self
    {
        return new static(static::getClass(), $persistence);
    }

    /** @inheritDoc */
    public function makeOne(array $attributes = []): Model
    {
        $attributes = array_merge($this->getDefaults(), $attributes);

        return new $this->class($attributes);
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
}
