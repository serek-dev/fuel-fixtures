<?php

declare(strict_types=1);

namespace Tests\Unit\Fuel;

use Orm\Model;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\Fuel\Proxy;

/** @covers \Stwarog\FuelFixtures\Fuel\Proxy */
final class ProxyTest extends TestCase
{
    /** @test */
    public function constructor(): void
    {
        $model = $this->getModel();

        $proxy = new Proxy($model);

        $this->assertInstanceOf(Proxy::class, $proxy);
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
