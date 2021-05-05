<?php
declare(strict_types=1);

namespace Tests\Unit;

use ArrayAccess;
use PHPUnit\Framework\TestCase;
use Stwarog\FuelFixtures\Proxy;

class ProxyTest extends TestCase
{
    public function testConstructor(): void
    {
        $model = $this->getModel();

        $proxy = new Proxy($model);

        $this->assertInstanceOf(Proxy::class, $proxy);
    }

    private function getModel(): ArrayAccess
    {
        return new class implements ArrayAccess {

            public function offsetExists($offset)
            {
                // TODO: Implement offsetExists() method.
            }

            public function offsetGet($offset)
            {
                // TODO: Implement offsetGet() method.
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
