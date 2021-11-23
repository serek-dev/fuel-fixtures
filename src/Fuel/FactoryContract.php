<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Countable;
use Faker\Generator;
use Orm\Model;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Exceptions\ConflictException;
use Stwarog\FuelFixtures\State;
use Stwarog\FuelFixtures\Reference;

# do not remove (phpstan)

/**
 * Interface FactoryContract
 * Countable - should return currents states count
 */
interface FactoryContract extends Countable, PersistenceContract
{
    public function __construct(?PersistenceContract $persistence = null, ?Generator $faker = null);

    /**
     * @return array<string, mixed>
     */
    public function getDefaults(): array;

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

    /**
     * @return array<string, array{0: string, 1: FactoryContract}|callable|Reference>
     */
    public function getStates(): array;

    /**
     * @param string|State|callable ...$states
     * @return static
     * @throws ConflictException
     */
    public function with(...$states): self;

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

    public function getDispatcher(): EventDispatcherInterface;

    /**
     * Creates random ids for given fields. Should not be called only with "make" methods.
     * @param string ...$fields
     * @return $this
     */
    public function withIdsFor(string ...$fields): self;

    public function withIds(): bool;

    /**
     * Determines, whenever state exists in getStates method
     *
     * @param string $state
     * @return bool
     */
    public function hasState(string $state): bool;

    /**
     * Determines, requested state has been used
     *
     * @param string $state
     * @return bool
     */
    public function inUse(string $state): bool;

    /**
     * Returns name of the class, we are creating for
     *
     * @return string
     */
    public function __toString(): string;

    public function reset(): void;
}
