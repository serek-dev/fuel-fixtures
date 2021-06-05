<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Closure;
use Faker\Generator;
use Orm\Model;
use Stwarog\FuelFixtures\Exceptions\StateNotFound;

interface FactoryContract
{
    public function __construct(?PersistenceContract $persistence = null, ?Generator $faker = null);

    /**
     * @return array
     */
    public function getDefaults(): array;

    /**
     * @param array<string, mixed> $attributes
     * @return Model<array>
     */
    public function makeOne(array $attributes = []): Model;

    /**
     * @param array{?string, ?mixed}[]|array[] $attributes
     * @return array<Model>
     */
    public function makeMany(array $attributes = [], int $count = 5): array;

    /**
     * @param array<string, mixed> $attributes
     * @return Model<array>
     */
    public function createOne(array $attributes = []): Model;

    /**
     * @param array<array<string, mixed>> $attributes
     * @return array<Model>
     */
    public function createMany(array $attributes = [], int $count = 5): array;

    /**
     * @return array<string, Closure>
     */
    public function getStates(): array;

    /**
     * @param string ...$states
     * @return static
     * @throws StateNotFound
     */
    public function with(string ...$states): self;

    /**
     * The class we are creating factory for
     * @return string
     */
    public static function getClass(): string;

    /**
     * @return static
     */
    public static function initialize(?PersistenceContract $persistence = null, ?Generator $faker = null): self;

    public function getPersistence(): PersistenceContract;

    public function getFaker(): Generator;

    /**
     * Creates random ids for given fields. Should not be called only with "make" methods.
     * @param string ...$fields
     * @return $this
     */
    public function withIdsFor(string ...$fields): self;
}
