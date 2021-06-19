<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Countable;
use Faker\Generator;
use Orm\Model;
use OutOfBoundsException;
use Stwarog\FuelFixtures\Exceptions\OutOfStateBound;
use Stwarog\FuelFixtures\Reference;
use Stwarog\FuelFixtures\State;

abstract class Factory implements FactoryContract, Countable
{
    private const IDS_STATE_KEY = '_ids';

    protected PersistenceContract $persistence;
    protected Generator $faker;

    /**
     * @var array<string, array> - key = stateName, value = attributes
     */
    private array $usedStates = [];

    /** @var array<string, mixed|callable> */
    private array $customStates = [];

    public function __construct(?PersistenceContract $persistence = null, ?Generator $faker = null)
    {
        $this->persistence = $persistence ?? new FuelPersistence();
        $this->faker = $faker ?? \Faker\Factory::create();
    }

    /**
     * @return static|self
     */
    final public static function initialize(?PersistenceContract $persistence = null, ?Generator $faker = null): self
    {
        return new static($persistence, $faker);
    }

    final public static function from(FactoryContract $factory): self
    {
        return self::initialize($factory->getPersistence(), $factory->getFaker());
    }

    /** @inheritDoc */
    final public function makeOne(array $attributes = []): Model
    {
        $attributes = array_merge($this->getDefaults(), $attributes);
        $class = static::getClass();

        $model = new $class($attributes);

        $definedStates = $this->getStates();
        $customStates = $this->customStates;
        $allStates = array_merge($definedStates, $customStates);

        // apply all closures
        foreach ($this->usedStates as $stateName => $stateAttributes) {
            $closure = $allStates[$stateName];
            $closure($model, !empty($stateAttributes) ? $stateAttributes : $attributes);
        }

        return $model;
    }

    /** @inheritDoc */
    final public function makeMany(array $attributes = [], int $count = 5): array
    {
        return array_map(fn() => $this->makeOne($attributes), range(1, $count));
    }

    /** @inheritDoc */
    final public function createOne(array $attributes = []): Model
    {
        $model = $this->makeOne($attributes);

        $this->persistence->persist($model);

        return $model;
    }

    /** @inheritDoc */
    final public function createMany(array $attributes = [], int $count = 5): array
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
    final public function with(...$states): FactoryContract
    {
        foreach ($states as $state) {
            $stateAsString = (string)$state;

            $chunks = explode('.', $stateAsString);
            $isNested = count($chunks) > 1;
            $subState = null;
            $shouldMakeMany = false;

            if ($isNested) {
                $stateAsString = $chunks[0];
                $subState = $chunks[1];
            }

            if ($hasBrackets = strpos($stateAsString, '[]') !== false) {
                $shouldMakeMany = true;
                $stateAsString = str_replace('[]', '', $stateAsString);
            }

            $stateAsArray = $this->getState($stateAsString);
            if ($stateAsArray instanceof Reference) {
                $stateAsArray = $stateAsArray->toArray();
            }

            if (is_array($stateAsArray)) {
                $parent = $this;
                $this->addCustomState(
                    $stateAsString,
                    function (Model $model, array $attributes = []) use (
                        $shouldMakeMany,
                        $subState,
                        $isNested,
                        $stateAsArray,
                        $parent
                    ) {
                        [$property, $factoryName] = $stateAsArray;
                        /** @var FactoryContract $subFactory */
                        $subFactory = $factoryName::from($parent);

                        if ($isNested && !empty($subState)) {
                            $subFactory->with($subState);
                        }

                        if ($shouldMakeMany) {
                            $model->$property = $subFactory->makeMany($attributes);
                            return;
                        }

                        $model->$property = $subFactory->makeOne($attributes);
                    }
                );
                $this->addUsedState($stateAsString);
                continue;
            }

            if ($state instanceof State) {
                $this->addUsedState($stateAsString, $state->getAttributes());
                continue;
            }

            $this->addUsedState($stateAsString);
        }

        return $this;
    }

    final public function setPersistence(PersistenceContract $strategy): void
    {
        $this->persistence = $strategy;
    }

    final public function getPersistence(): PersistenceContract
    {
        return $this->persistence;
    }

    final public function getFaker(): Generator
    {
        return $this->faker;
    }

    final public function count(): int
    {
        return count($this->usedStates);
    }

    /**
     * @param array<string, mixed> $attributes
     * @return Model
     */
    final public function __invoke(array $attributes = []): Model
    {
        return $this->makeOne($attributes);
    }

    final public function withIdsFor(string ...$fields): self
    {
        $this->addUsedState(self::IDS_STATE_KEY);

        $this->addCustomState(
            self::IDS_STATE_KEY,
            function (Model $model) use ($fields) {
                foreach ($fields as $field) {
                    $model->$field = $this->faker->numberBetween(1, 10000);
                }
            }
        );
        return $this;
    }

    final public function withIds(): bool
    {
        return isset($this->usedStates[self::IDS_STATE_KEY]);
    }

    final public function hasState(string $state): bool
    {
        return isset($this->getStates()[$state]);
    }

    /**
     * @param string $state
     * @return callable|array{0: string, 1: FactoryContract}|Reference
     * @throws OutOfBoundsException
     */
    private function getState(string $state)
    {
        if (!$this->hasState($state)) {
            throw OutOfStateBound::create($state);
        }
        return $this->getStates()[$state];
    }

    private function addUsedState(string $stateName, array $attributes = []): void
    {
        $this->usedStates[$stateName] = $attributes;
    }

    private function addCustomState(string $stateName, callable $callable): void
    {
        $this->customStates[$stateName] = $callable;
    }
}
