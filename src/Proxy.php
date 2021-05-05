<?php
declare(strict_types=1);

namespace Stwarog\FuelFixtures;

use ArrayAccess;
use Orm\Model;

final class Proxy extends Model
{
    private ArrayAccess $model;

    public function __construct(ArrayAccess $model)
    {
        $this->model = $model;
    }
}
