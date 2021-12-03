<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\DependencyInjection;

use Faker\Factory as FakerFactory;
use Faker\Generator;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Events\NullObjectDispatcher;
use Stwarog\FuelFixtures\Fuel\FuelPersistence;
use Stwarog\FuelFixtures\Fuel\PersistenceContract;

final class Config implements ConfigContract
{
    private PersistenceContract $persistence;
    private Generator $faker;
    private EventDispatcherInterface $dispatcher;
    private ContainerInterface $container;

    public function __construct(
        ?PersistenceContract $persistence = null,
        ?Generator $faker = null,
        ?EventDispatcherInterface $eventDispatcher = null,
        ?ContainerInterface $container = null
    ) {
        $this->faker = $faker ?? FakerFactory::create();
        $this->dispatcher = $eventDispatcher ?? new NullObjectDispatcher();
        $this->persistence = $persistence ?? new FuelPersistence($this->dispatcher);
        $this->container = $container ?? new NullObjectContainer();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getPersistence(): PersistenceContract
    {
        return $this->persistence;
    }

    public function getFaker(): Generator
    {
        return $this->faker;
    }

    public function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
