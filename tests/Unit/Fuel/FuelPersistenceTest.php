<?php

declare(strict_types=1);

namespace Tests\Unit\Fuel;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Events\AfterPersisted;
use Stwarog\FuelFixtures\Events\BeforePersisted;
use Stwarog\FuelFixtures\Fuel\FuelPersistence;
use Stwarog\FuelFixtures\Fuel\PersistenceContract;
use Tests\Unit\Mocks\ModelImitation;

/** @covers \Stwarog\FuelFixtures\Fuel\FuelPersistence */
final class FuelPersistenceTest extends TestCase
{
    /** @test */
    public function constructor(): void
    {
        $sut = new FuelPersistence();

        $this->assertInstanceOf(PersistenceContract::class, $sut);
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut->getDispatcher());
    }

    /** @test */
    public function persist_shouldDispatchModelEvents(): void
    {
        // Given dispatcher that should be called
        $dispatcher = $this->getDispatcher();

        // and FuelPersistence
        $sut = new FuelPersistence($dispatcher);

        // When persist is called on a model
        $model = new ModelImitation();
        $sut->persist($model);

        // Then expected events should be dispatched
        if (property_exists($dispatcher, 'events')) {
            $this->assertCount(2, $dispatcher->events);

            $event1 = $dispatcher->events[0];
            $event2 = $dispatcher->events[1];

            $this->assertInstanceOf(BeforePersisted::class, $event1);
            $this->assertInstanceOf(ModelImitation::class, $event1->getModel());
            $this->assertInstanceOf(AfterPersisted::class, $event2);
            $this->assertInstanceOf(ModelImitation::class, $event2->getModel());
        }
    }

    private function getDispatcher(): EventDispatcherInterface
    {
        return new class implements EventDispatcherInterface {
            public array $events = [];

            public function dispatch(object $event): object
            {
                $this->events[] = $event;
                return $event;
            }
        };
    }
}
