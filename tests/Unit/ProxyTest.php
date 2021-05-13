<?php

declare(strict_types=1);

namespace Tests\Unit;

use Orm\Model;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\Exceptions\OutOfBound;
use Stwarog\FuelFixtures\Proxy;

/** @covers \Stwarog\FuelFixtures\Proxy */
final class ProxyTest extends TestCase
{
    /** @test */
    public function constructor(): void
    {
        $model = $this->getModel();

        $proxy = new Proxy($model);

        $this->assertInstanceOf(Proxy::class, $proxy);
    }

    /** @test */
    public function getPropertyByMagicMethod_propertyNotExists_throwsException(): void
    {
        // Expect
        $this->expectException(OutOfBound::class);

        // Given not existing property
        $property = 'not_existing_property';
        $model = $this->getModel();
        $proxy = new Proxy($model);

        // When attempts to get
        /** @phpstan-ignore-next-line */
        $proxy->$property;
    }

    /** @test */
    public function getPropertyByArrayAccess_propertyNotExists_throwsException(): void
    {
        // Expect
        $this->expectException(OutOfBound::class);

        // Given not existing property
        $property = 'not_existing_property';
        $model = $this->getModel();
        $proxy = new Proxy($model);

        // When attempts to get
        /** @phpstan-ignore-next-line */
        $proxy[$property];
    }

    /**
     * @test
     * @return array{Proxy, string, mixed}
     */
    public function getPropertyByArrayAccess_propertyExists(): array
    {
        // Given
        $value = 123;
        $property = 'existing_property';
        $model = $this->getModel([$property => $value]);
        $proxy = new Proxy($model);

        // When property is defined & Then returns value
        $actual = $proxy[$property];
        $this->assertSame($value, $actual);

        return [$proxy, $property, $value];
    }

    /**
     * @test
     * @param array{Proxy, string, mixed} $stack
     * @depends getPropertyByArrayAccess_propertyExists
     */
    public function getPropertyByMagicMethod_propertyExists(array $stack): void
    {
        [$proxy, $property, $value] = $stack;

        // When property is defined & Then returns value
        $actual = $proxy->$property;
        $this->assertSame($value, $actual);
    }

    /**
     * @param array<string, int> $data
     * @return Model<mixed>
     */
    private function getModel(array $data = []): Model
    {
        $class = new class extends Model {
            /** @phpstan-ignore-next-line */
            protected static $_properties = ['id', 'existing_property'];
        };

        return new $class($data);
    }
}
