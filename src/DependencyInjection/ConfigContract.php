<?php

namespace Stwarog\FuelFixtures\DependencyInjection;

use Faker\Generator;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Fuel\PersistenceContract;

interface ConfigContract
{
    public function getPersistence(): PersistenceContract;

    public function getFaker(): Generator;

    public function getDispatcher(): EventDispatcherInterface;

    public function getContainer(): ContainerInterface;
}
