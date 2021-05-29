<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Stwarog\FuelFixtures\Exceptions\StateNotFound;

interface FactoryContract
{
    /**
     * @return array
     */
    public function getDefaults(): array;

    /**
     * @param array<mixed> $attributes
     * @return Proxy<array>
     */
    public function makeOne(array $attributes = []): Proxy;

    /**
     * @param array{?string, ?mixed}[]|array[] $attributes
     * @return array<Proxy>
     */
    public function makeMany(array $attributes = [], int $count = 5): array;

    /**
     * @param array<mixed> $attributes
     * @return Proxy<array>
     */
    public function createOne(array $attributes = []): Proxy;

    /**
     * @param array<mixed> $attributes
     * @return array<Proxy>
     */
    public function createMany(array $attributes = [], int $count = 5): array;

    /**
     * @return array<string, string>
     */
    public function getStates(): array;

    /**
     * @param string $state
     * @return static
     * @throws StateNotFound
     */
    public function with(string $state): self;

    /**
     * The class we are creating factory for
     * @return string
     */
    public static function getClass(): string;

    /**
     * @return static
     */
    public static function initialize(?PersistenceContract $persistence = null): self;
}
