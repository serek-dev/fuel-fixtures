<?php
declare(strict_types=1);

namespace Stwarog\FuelFixtures;

use ArrayAccess;
use Orm\Model;
use Stwarog\FuelFixtures\Exceptions\OutOfBound;

final class Proxy extends Model implements ArrayAccess
{
    private ArrayAccess $model;

    public function __construct(ArrayAccess $model)
    {
        $this->model = $model;
    }

    /**
     * Fetch a property or relation
     *
     * @param   string
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

    public function offsetExists($offset): bool
    {
        return $this->model->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->$offset;
    }
}
