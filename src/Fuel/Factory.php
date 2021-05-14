<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use ErrorException;
use Exception;

class Factory implements FactoryContract
{
    private string $class;

    private PersistenceContract $persistence;

    public function __construct(string $class, ?PersistenceContract $persistence = null)
    {
        $this->class = $class;
        $this->persistence = $persistence ?? new FuelPersistence();
    }

    /**
     * @param array<string, mixed> $attributes
     * @return Proxy<array>
     * @throws Exception
     */
    public function createOne(array $attributes = []): Proxy
    {
        $model = $this->makeOne($attributes);

        $this->persistence->persist($model);

        return $model;
    }

    /**
     * @param array<string, mixed> $attributes
     * @return Proxy<array>
     * @throws ErrorException
     */
    public function makeOne(array $attributes = []): Proxy
    {
        $attributes = array_merge($this->getDefaults(), $attributes);

        return new Proxy(
            new ${$this->class}($attributes)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(): array
    {
        return [];
    }

    /**
     * @param array<string, mixed> $attributes
     * @param int $count
     * @return array<Proxy>
     * @throws ErrorException
     */
    public function createMany(array $attributes = [], int $count = 5): array
    {
        $models = $this->makeMany($attributes, $count);

        $this->persistence->persist(...$models);

        return $models;
    }

    /**
     * @param array<string, mixed> $attributes
     * @param int $count
     * @return array<Proxy>
     * @throws ErrorException
     */
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
