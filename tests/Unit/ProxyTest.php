<?php
declare(strict_types=1);

namespace Tests\Unit;

use ArrayAccess;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\Exceptions\OutOfBound;
use Stwarog\FuelFixtures\Proxy;

class ProxyTest extends TestCase
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

        // Given
        $property = 'not_existing_property';
        $offsetExists = false;
        $model = $this->getMockForAbstractClass(ArrayAccess::class);
        $model->method('offsetExists')->willReturn($offsetExists);
        $proxy = new Proxy($model);

        // When property is not defined
        $proxy->$property;
    }

    /** @test */
    public function getPropertyByArrayAccess_propertyNotExists_throwsException(): void
    {
        // Expect
        $this->expectException(OutOfBound::class);

        // Given
        $property = 'not_existing_property';
        $offsetExists = false;
        $model = $this->getMockForAbstractClass(ArrayAccess::class);
        $model->method('offsetExists')->willReturn($offsetExists);
        $proxy = new Proxy($model);

        // When property is not defined
        $proxy[$property];
    }

    /**
     * @test
     * @return array<Proxy, string, mixed>
     */
    public function getPropertyByArrayAccess_propertyExists(): array
    {
        // Given
        $value = 123;
        $property = 'existing_property';
        $offsetExists = true;
        $model = $this->getMockForAbstractClass(ArrayAccess::class);
        $model->method('offsetExists')->willReturn($offsetExists);
        $model->method('offsetGet')->willReturn($value);
        $proxy = new Proxy($model);

        // When property is defined & Then returns value
        $actual = $proxy[$property];
        $this->assertSame($value, $actual);

        return [$proxy, $property, $value];
    }

    /**
     * @test
     * @param array<Proxy, string, mixed> $stack
     * @depends getPropertyByArrayAccess_propertyExists
     */
    public function getPropertyByMagicMethod_propertyExists(array $stack): void
    {
        [$proxy, $property, $value] = $stack;

        // When property is defined & Then returns value
        $actual = $proxy->$property;
        $this->assertSame($value, $actual);
    }

    private function getModel(): ArrayAccess
    {
        return new class implements ArrayAccess {

            public function offsetExists($offset)
            {
                return false;
            }

            public function offsetGet($offset)
            {
                return null;
            }

            public function offsetSet($offset, $value)
            {
                // TODO: Implement offsetSet() method.
            }

            public function offsetUnset($offset)
            {
                // TODO: Implement offsetUnset() method.
            }
        };
    }
}
