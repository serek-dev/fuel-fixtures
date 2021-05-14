<?php

declare(strict_types=1);

namespace Stwarog\FuelFixtures\Fuel;

use Exception;
use Orm\Model;

class FuelPersistence implements PersistenceContract
{
    /**
     * @param Model<array> ...$models
     * @throws Exception
     */
    public function persist(Model ...$models): void
    {
        foreach ($models as $model) {
            $model->save();
        }
    }
}
