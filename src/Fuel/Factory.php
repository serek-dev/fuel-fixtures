<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

abstract class Factory implements FactoryContract
{
    private string $class;

    protected PersistenceContract $persistence;

    protected function __construct(string $class, ?PersistenceContract $persistence = null)
    {
        $this->class = $class;
        $this->persistence = $persistence ?? new FuelPersistence();
    }

    /** @inheritDoc */
    public function createOne(array $attributes = []): Proxy
    {
        $model = $this->makeOne($attributes);

        $this->persistence->persist($model);

        return $model;
    }

    /** @inheritDoc */
    public function makeOne(array $attributes = []): Proxy
    {
        $attributes = array_merge($this->getDefaults(), $attributes);

        return new Proxy(
            new ${$this->class}($attributes)
        );
    }

    /** @inheritDoc */
    public function createMany(array $attributes = [], int $count = 5): array
    {
        $models = $this->makeMany($attributes, $count);

        $this->persistence->persist(...$models);

        return $models;
    }

    /** @inheritDoc */
    public function makeMany(array $attributes = [], int $count = 5): array
    {
        return array_map(fn() => $this->makeOne($attributes), range(0, $count));
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
