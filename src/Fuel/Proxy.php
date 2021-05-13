<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use ArrayAccess;
use Orm\Model;
use Stwarog\FuelFixtures\Exceptions\OutOfBound;
use Stwarog\FuelFixtures\ProxyContract;

final class Proxy extends Model implements ProxyContract
{
    private ArrayAccess $model;

    public function __construct(ArrayAccess $model)
    {
        $this->model = $model;
    }

    /**
     * Fetch a property or relation
     *
     * @param   string $property
     * @return  mixed
     */
    public function & __get($property)
    {
        if (!$this->model->offsetExists($property)) {
            throw OutOfBound::create($property);
        }

        $value = $this->model->offsetGet($property);
        return $value;
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->model->offsetExists($offset);
    }

    /**
     * @param string|int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
}
