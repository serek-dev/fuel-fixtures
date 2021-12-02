<?php

declare(strict_types=1);

namespace Tests\Unit\Fuel;

use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Stwarog\FuelFixtures\Fuel\PersistenceContract;
use Stwarog\FuelFixtures\Fuel\UowPersistence;
use Stwarog\Uow\DBConnectionInterface;
use Stwarog\Uow\UnitOfWork\UnitOfWork;
use Stwarog\UowFuel\FuelEntityManager;

/** @covers \Stwarog\FuelFixtures\Fuel\UowPersistence */
final class UowPersistenceTest extends TestCase
{
    /** @test */
    public function constructor(): void
    {
        $connection = $this->createMock(DBConnectionInterface::class);
        $em = new FuelEntityManager($connection, new UnitOfWork());
        $sut = new UowPersistence($em);

        $this->assertInstanceOf(PersistenceContract::class, $sut);
        $this->assertInstanceOf(EventDispatcherInterface::class, $sut->getDispatcher());
    }
}
