<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\DependencyInjection\Config;
use Stwarog\FuelFixtures\DependencyInjection\NullObjectContainer;
use Stwarog\FuelFixtures\Events\NullObjectDispatcher;
use Stwarog\FuelFixtures\Fuel\FuelPersistence;

/** @covers \Stwarog\FuelFixtures\DependencyInjection\Config */
final class ConfigTest extends TestCase
{
    /** @test */
    public function constructor_shouldCreateDefaultImplementations(): void
    {
        // Given Config without any params
        $sut = new Config();

        // When created & Then default concrete implementation should be returned
        $this->assertInstanceOf(Generator::class, $sut->getFaker());
        $this->assertInstanceOf(NullObjectDispatcher::class, $sut->getDispatcher());
        $this->assertInstanceOf(NullObjectContainer::class, $sut->getContainer());
        $this->assertInstanceOf(FuelPersistence::class, $sut->getPersistence());
    }

    /** @test */
    public function constructor_argsPassed_shouldReturnPassedInstances(): void
    {
        // Given Config with concrete implementations
        $generator = Factory::create();
        $dispatcher = new NullObjectDispatcher();
        $container = new NullObjectContainer();
        $persistence = new FuelPersistence();
        $sut = new Config($persistence, $generator, $dispatcher, $container);

        // When created & Then default concrete implementation should be returned
        $this->assertSame($generator, $sut->getFaker());
        $this->assertSame($container, $sut->getContainer());
        $this->assertSame($persistence, $sut->getPersistence());
        $this->assertSame($dispatcher, $sut->getDispatcher());
    }
}
